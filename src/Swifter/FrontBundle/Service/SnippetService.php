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

    private function getSnippetsFromPageBlockContent(PageBlock $pageBlock)
    {
        $matchedSnippets = null;
        preg_match_all('/\\[\\[(.*?)\\]\\]/', $pageBlock->getContent(), $matchedSnippets);

        $foundSnippets = array();

        /* $snippets[0] returns match like this [[(.*?)]], we need (.*?)., so we take [1] */
        foreach ($matchedSnippets[1] as $matchedSnippet)
        {
            $foundSnippets[] = $matchedSnippet;
        }

        return $foundSnippets;
    }
}