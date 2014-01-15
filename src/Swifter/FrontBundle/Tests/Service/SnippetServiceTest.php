<?php

namespace Swifter\FrontBundle\Tests\Service;

use Swifter\FrontBundle\Entity\Page;
use Swifter\FrontBundle\Entity\PageBlock;
use Swifter\FrontBundle\Service\SnippetService;

class SnippetServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testShouldLeavePageBlockWithChangesIfNoSnippets()
    {
        /* Given. */
        $pageBlockContent = "Page block content with no snippets";

        $pageBlock = new PageBlock();
        $pageBlock->setContent($pageBlockContent);
        $pageBlocks = array($pageBlock);

        $pageBlocksPC = $this->getMock('Doctrine\Common\Collections\Collection');
        $pageBlocksPC->expects($this->any())
            ->method('toArray')
            ->will($this->returnValue($pageBlocks));

        $page = new Page();
        $page->setPageBlocks($pageBlocksPC);

        $container = $this->getMockBuilder('Symfony\Component\DependencyInjection\ContainerInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        /* This test doesn't need entityManager and container. */
        $snippetService = new SnippetService($container, $em);

        /* When. */
        $snippetService->resolveSnippetsForPage($page);

        /* Then. */
        $actualPageBlocksArray = $page->getPageBlocks()->toArray();
        $this->assertEquals($pageBlockContent, $actualPageBlocksArray[0]->getContent());

    }
}