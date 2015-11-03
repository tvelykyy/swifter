<?php

namespace Swifter\FrontBundle\Tests\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Swifter\CommonBundle\Entity\Block;
use Swifter\CommonBundle\Entity\Page;
use Swifter\CommonBundle\Entity\PageBlock;
use Swifter\CommonBundle\Service\PageBlockService;

/**
 * Test Matrix
 *      A   B   C
 * 1.   +   +   +
 * 2.   +   -   -
 * 3.   +   -   +
 * 4.   +   +   -
 * 5.   -   +   +
 * 6.   -   +   -
 * 7.   -   -   +
 */
class PageBlockServiceTest extends WebTestCase
{
    const PARENT = "parent";
    const CHILD = "child";
    const GRAND_CHILD = "child";

    private $pageBlockService;

    public function __construct()
    {
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $this->pageBlockService = new PageBlockService($em);
    }

    public function testCase1()
    {
        /* Given. */
        $grandChild = $this->initPagesWithBlocksContent(self::PARENT, self::CHILD, self::GRAND_CHILD);

        /* When. */
        $this->pageBlockService->mergePageBlocksWithParents($grandChild);

        /* Then. */
        $this->assertEquals(self::CHILD, $this->getContent($grandChild));
    }

    public function testCase2()
    {
        /* Given. */
        $grandChild = $this->initPagesWithBlocksContent(self::PARENT, null, null);

        /* When. */
        $this->pageBlockService->mergePageBlocksWithParents($grandChild);

        /* Then. */
        $this->assertEquals(self::PARENT, $this->getContent($grandChild));
    }

    public function testCase3()
    {
        /* Given. */
        $grandChild = $this->initPagesWithBlocksContent(self::PARENT, null, self::GRAND_CHILD);

        /* When. */
        $this->pageBlockService->mergePageBlocksWithParents($grandChild);

        /* Then. */
        $this->assertEquals(self::GRAND_CHILD, $this->getContent($grandChild));
    }

    public function testCase4()
    {
        /* Given. */
        $grandChild = $this->initPagesWithBlocksContent(self::PARENT, self::CHILD, null);

        /* When. */
        $this->pageBlockService->mergePageBlocksWithParents($grandChild);

        /* Then. */
        $this->assertEquals(self::CHILD, $this->getContent($grandChild));
    }

    public function testCase5()
    {
        /* Given. */
        $grandChild = $this->initPagesWithBlocksContent(null, self::CHILD, self::GRAND_CHILD);

        /* When. */
        $this->pageBlockService->mergePageBlocksWithParents($grandChild);

        /* Then. */
        $this->assertEquals(self::GRAND_CHILD, $this->getContent($grandChild));
    }

    public function testCase6()
    {
        /* Given. */
        $grandChild = $this->initPagesWithBlocksContent(null, self::CHILD, null);

        /* When. */
        $this->pageBlockService->mergePageBlocksWithParents($grandChild);

        /* Then. */
        $this->assertEquals(self::CHILD, $this->getContent($grandChild));
    }

    public function testCase7()
    {
        /* Given. */
        $grandChild = $this->initPagesWithBlocksContent(null, null, self::GRAND_CHILD);

        /* When. */
        $this->pageBlockService->mergePageBlocksWithParents($grandChild);

        /* Then. */
        $this->assertEquals(self::GRAND_CHILD, $this->getContent($grandChild));
    }

    private function initPagesWithBlocksContent($parentContent, $childContent, $grandChildContent)
    {
        $parent = $this->initPageWithPageBlockContentAndParent($parentContent);
        $child = $this->initPageWithPageBlockContentAndParent($childContent, $parent);
        $grandChild = $this->initPageWithPageBlockContentAndParent($grandChildContent, $child);

        return $grandChild;
    }
    private function initPageWithPageBlockContentAndParent($content, $parent = null)
    {
        $page = new Page();
        if ($content)
        {
            $page->setPageBlocks($this->initPageBlocksWithOneBlock($content));
        } else
        {
            $page->setPageBlocks(new ArrayCollection());
        }

        $page->setParent($parent);

        return $page;
    }

    private function initPageBlocksWithOneBlock($content)
    {
        $block = new Block();
        $block->setId(1);
        $block->setTitle('MAIN');

        $pageBlock = new PageBlock();
        $pageBlock->setBlock($block);
        $pageBlock->setContent($content);

        return new ArrayCollection([$pageBlock]);
    }

    private function getContent($page)
    {
        return $page->getPageBlocks()->first()->getContent();
    }
}