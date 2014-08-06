<?php

namespace Swifter\CommonBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;

class PageBlockService
{
    public function mergePageBlocksWithParents($page)
    {
        $mergedPageBlocks = $page->getPageBlocks();
        $parent = $page->getParent();

        while (isset($parent))
        {
            $deficientPageBlocks = $this->getDeficientBlocksFromParent($parent, $mergedPageBlocks);
            $mergedPageBlocks = new ArrayCollection(array_merge($mergedPageBlocks->toArray(), $deficientPageBlocks->toArray()));
            $parent = $parent->getParent();
        }

        $page->setPageBlocks($mergedPageBlocks);
    }

    /**
     * Deficient blocks mean that it is absent in page block mapping. If pageBlock exists in current page and parent page
     * it would not be treated as deficient and would not be returned.
     */
    private function getDeficientBlocksFromParent($parent, $childPageBlocks)
    {
        $deficientBlocks = $parent->getPageBlocks()->filter(
            function ($pageBlock) use ($childPageBlocks) {
                return !$childPageBlocks->exists(
                    function ($index, $childPageBlock) use ($pageBlock) {
                        return $childPageBlock->getBlock()->getTitle() === $pageBlock->getBlock()->getTitle();
                    }
                );
            }
        );
        return $deficientBlocks;
    }

}