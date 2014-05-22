<?php

namespace Swifter\FrontBundle\Tests\Service;

use Swifter\CommonBundle\Entity\Page;
use Swifter\CommonBundle\Entity\PageBlock;
use Swifter\CommonBundle\Entity\Snippet;
use Swifter\CommonBundle\Entity\Template;
use Swifter\FrontBundle\Service\SnippetService;

class SnippetServiceTest extends \PHPUnit_Framework_TestCase
{
    /* Symfony constants. */
    const ENTITY_MANAGER_CLASS = 'Doctrine\ORM\EntityManager';
    const CONTAINER_INTERFACE = 'Symfony\Component\DependencyInjection\ContainerInterface';
    const TWIG_ENGINE_CLASS = 'Symfony\Bundle\TwigBundle\TwigEngine';
    const DOCTRINE_COLLECTION_CLASS = 'Doctrine\Common\Collections\Collection';

    /* Tested behaviour constants. */
    const DEV_TEST_SERVICE_RESULT = 'DevTestServiceResult';
    const SNIPPET_TITLE = 'SNIPPET';
    const SKELETON_PAGE_BLOCK_CONTENT = 'Page block content {} with snippet.';
    const RESOLVED_SNIPPET_VALUE = 'Resolved Snippet';

    const SNIPPET_SERVICE = 'swifter_front.service.devtest';
    const SNIPPET_METHOD = 'getPages';
    const SNIPPET_PARAMS = '{"start":2,"end":5}';

    public function testShouldLeavePageBlockWithNoChangesIfNoSnippets()
    {
        /* Given. */
        $pageBlockContent = 'Page block content with no snippets.';
        $page = $this->initPageWithOnePageBlock($pageBlockContent);

        $container = $this->getMockBuilder(self::CONTAINER_INTERFACE)
            ->disableOriginalConstructor()
            ->getMock();
        $em = $this->getMockBuilder(self::ENTITY_MANAGER_CLASS)
            ->disableOriginalConstructor()
            ->getMock();

        $snippetService = new SnippetService($container, $em);

        /* When. */
        $snippetService->resolveSnippetsForPage($page, array());

        /* Then. */
        $actualPageBlocksArray = $page->getPageBlocks()->toArray();
        $this->assertEquals($pageBlockContent, $actualPageBlocksArray[0]->getContent());
    }

    public function testShouldResolveSnippetWithoutClientParams()
    {
        /* Given. */
        $params = json_decode(self::SNIPPET_PARAMS, true);

        list($resolvedPageBlockContent, $page, $snippetService) = $this->initTestContext($params);

        /* When. */
        $snippetService->resolveSnippetsForPage($page, array());

        /* Then. */
        $actualPageBlocksArray = $page->getPageBlocks()->toArray();
        $this->assertEquals($resolvedPageBlockContent, $actualPageBlocksArray[0]->getContent());
    }

    public function testShouldResolveSnippetWithClientParams()
    {
        /* Given. */
        $params = array('start' => 1, 'end' => 2);

        list($resolvedPageBlockContent, $page, $snippetService) = $this->initTestContext($params);

        /* When. */
        $snippetService->resolveSnippetsForPage($page, $params);

        /* Then. */
        $actualPageBlocksArray = $page->getPageBlocks()->toArray();
        $this->assertEquals($resolvedPageBlockContent, $actualPageBlocksArray[0]->getContent());
    }


    private function initTestContext($params)
    {
        $initialPageBlockContent = str_replace('{}', '[[' . self::SNIPPET_TITLE . ']]', self::SKELETON_PAGE_BLOCK_CONTENT);
        $resolvedPageBlockContent = str_replace('{}', self::RESOLVED_SNIPPET_VALUE, self::SKELETON_PAGE_BLOCK_CONTENT);

        $page = $this->initPageWithOnePageBlock($initialPageBlockContent);

        $template = $this->initTemplate();

        $snippet = $this->initSnippetWithTemplate($template);

        $em = $this->initEntityManager(self::SNIPPET_TITLE, $snippet);

        $devTestService = $this->initDevTestService($snippet->getMethod(), $params['start'], $params['end'], self::DEV_TEST_SERVICE_RESULT);
        $twigEngine = $this->initTwigEngine($template, self::DEV_TEST_SERVICE_RESULT, self::RESOLVED_SNIPPET_VALUE);
        $container = $this->initContainer($snippet, $devTestService, $twigEngine);

        $snippetService = new SnippetService($container, $em);
        return array($resolvedPageBlockContent, $page, $snippetService);
    }

    private function initPageWithOnePageBlock($pageBlockContent)
    {
        $pageBlock = new PageBlock();
        $pageBlock->setContent($pageBlockContent);
        $pageBlocks = array($pageBlock);

        $pageBlocksPC = $this->getMock(self::DOCTRINE_COLLECTION_CLASS);
        $pageBlocksPC->expects($this->any())
            ->method('toArray')
            ->will($this->returnValue($pageBlocks));

        $page = new Page();
        $page->setPageBlocks($pageBlocksPC);

        return $page;
    }

    private function initTemplate()
    {
        $template = new Template();
        $template->setPath('template/path');

        return $template;
    }

    private function initSnippetWithTemplate($template)
    {
        $snippet = new Snippet();
        $snippet->setService(self::SNIPPET_SERVICE);
        $snippet->setMethod(self::SNIPPET_METHOD);
        $snippet->setParams(self::SNIPPET_PARAMS);
        $snippet->setTemplate($template);

        return $snippet;
    }

    private function initEntityManager($snippetTitle, $snippet)
    {
        $em = $this->getMock(self::ENTITY_MANAGER_CLASS, array('getRepository', 'findOneByTitle'), array(), '', false);
        $em->expects($this->any())
            ->method('findOneByTitle')
            ->with($snippetTitle)
            ->will($this->returnValue($snippet));

        $em->expects($this->any())
            ->method($this->anything())
            ->will($this->returnValue($em));

        return $em;
    }

    private function initDevTestService($method, $param1, $param2, $devTestServiceResult)
    {
        $devTestService = $this->getMock('DevTestService', array($method), array(), '', false);
        $devTestService->expects($this->once())
            ->method($method)
            ->with($param1, $param2)
            ->will($this->returnValue($devTestServiceResult));

        return $devTestService;
    }

    private function initTwigEngine($template, $devTestServiceResult, $resolvedSnippetValue)
    {
        $twigEngine = $this->getMockBuilder(self::TWIG_ENGINE_CLASS)
            ->disableOriginalConstructor()
            ->getMock();
        $twigEngine->expects($this->any())
            ->method('render')
            ->with($template->getPath(), array('params' => $devTestServiceResult))
            ->will($this->returnValue($resolvedSnippetValue));

        return $twigEngine;
    }

    private function initContainer($snippet, $devTestService, $twigEngine)
    {
        $container = $this->getMockBuilder(self::CONTAINER_INTERFACE)
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

        return $container;
    }

}