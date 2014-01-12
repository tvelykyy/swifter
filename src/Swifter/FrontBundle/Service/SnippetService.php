<?php

namespace Swifter\FrontBundle\Service;

use Swifter\FrontBundle\Entity\Page;
use Swifter\FrontBundle\Entity\PageBlock;

class SnippetService
{
    public function resolveSnippetsForPage(Page $page)
    {
        foreach($page->getPageBlocks()->toArray() as $pageBlock)
        {
            $pageBlock = $this->resolveSnippetsForPageBlock($pageBlock);
        }
    }

    private function resolveSnippetsForPageBlock(PageBlock $pageBlock)
    {
        $snippets = $this->getSnippetsFromPageBlockContent($pageBlock);
    }

    private function getSnippertsFromPageBlockContent(PageBlock $pageBlock)
    {

    }
}