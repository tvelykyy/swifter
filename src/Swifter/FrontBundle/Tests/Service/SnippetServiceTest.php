<?php

namespace Swifter\FrontBundle\Tests\Service;

use Swifter\CommonBundle\Entity\Page;
use Swifter\CommonBundle\Entity\PageBlock;
use Swifter\CommonBundle\Entity\Snippet;
use Swifter\CommonBundle\Entity\Template;
use Swifter\FrontBundle\Service\SnippetService;

class SnippetServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldLeavePageBlockWithChangesIfNoSnippets()
    {
        /* Given. */
        $pageBlockContent = 'Page block content with no snippets.';
        $page = $this->initPageWithOnePageBlock($pageBlockContent);

        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $snippetService = new SnippetService($container, $em);

        /* When. */
        $snippetService->resolveSnippetsForPage($page, array());

        /* Then. */
        $actualPageBlocksArray = $page->getPageBlocks()->toArray();
        $this->assertEquals($pageBlockContent, $actualPageBlocksArray[0]->getContent());
    }

    public function testShouldResolveSnippet()
    {
        /* Given. */
        $devTestServiceResult = 'DevTestServiceResult';

        $snippetTitle = 'SNIPPET';
        $basePageBlockContent = 'Page block content {} with snippet.';
        $initialPageBlockContent = str_replace('{}', '[['. $snippetTitle. ']]', $basePageBlockContent);

        $resolvedSnippetValue = 'Resolved Snippet';
        $resolvedPageBlockContent = str_replace('{}', $resolvedSnippetValue, $basePageBlockContent);

        $page = $this->initPageWithOnePageBlock($initialPageBlockContent);

        $template = new Template();
        $template->setPath('template/path');

        $snippet = new Snippet();
        $snippet->setService('swifter_front.service.devtest');
        $snippet->setMethod('getPages');
        $snippet->setParams('{"start":2,"end":5}');
        $snippet->setTemplate($template);

        $em = $this->getMock('Doctrine\ORM\EntityManager', array('getRepository', 'findOneByTitle'), array(), '', false);
        $em->expects($this->any())
            ->method('findOneByTitle')
            ->with($snippetTitle)
            ->will($this->returnValue($snippet));

        $em ->expects($this->any())
            ->method($this->anything())
            ->will($this->returnValue($em));

        $devTestService = $this->getMock('DevTestService', array($snippet->getMethod()), array(), '', false);
        $devTestService->expects($this->once())
            ->method($snippet->getMethod())
            ->will($this->returnValue($devTestServiceResult));

        $twigEngine = $this->getMockBuilder('Symfony\Bundle\TwigBundle\TwigEngine')
            ->disableOriginalConstructor()
            ->getMock();
        $twigEngine->expects($this->any())
            ->method('render')
            ->with($template->getPath(), array('params' => $devTestServiceResult))
            ->will($this->returnValue($resolvedSnippetValue));

        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects($this->at(0))
            ->method('get')
            ->with($snippet->getService())
            ->will($this->returnValue($devTestService));
        $container->expects($this->at(1))
            ->method('get')
            ->with('templating')
            ->will($this->returnValue($twigEngine));

        $snippetService = new SnippetService($container, $em);

        /* When. */
        $snippetService->resolveSnippetsForPage($page, array());

        /* Then. */
        $actualPageBlocksArray = $page->getPageBlocks()->toArray();
        $this->assertEquals($resolvedPageBlockContent, $actualPageBlocksArray[0]->getContent());
    }

    private function initPageWithOnePageBlock($pageBlockContent)
    {
        $pageBlock = new PageBlock();
        $pageBlock->setContent($pageBlockContent);
        $pageBlocks = array($pageBlock);

        $pageBlocksPC = $this->getMock('Doctrine\Common\Collections\Collection');
        $pageBlocksPC->expects($this->any())
            ->method('toArray')
            ->will($this->returnValue($pageBlocks));

        $page = new Page();
        $page->setPageBlocks($pageBlocksPC);

        return $page;
    }
}