<?php

namespace Swifter\FrontBundle\Service;

use Swifter\FrontBundle\Entity\Page;
use Swifter\FrontBundle\Entity\PageBlock;
use Swifter\FrontBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class SnippetService
{
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

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

        foreach($snippets as $snippet)
        {
            $parsedSnippert = $this->parseSnippet($snippet);
        }
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

    private function parseSnippet($snippetSource)
    {
        $tokens = explode('?', $snippetSource);

        $executionPart = explode('#', $tokens[0]);
        $serviceName = $executionPart[0];
        $methodName = $executionPart[1];

        $service = $this->container->get($serviceName);

        $result = call_user_func_array(array($service, $methodName), array());

        $templateId = $executionPart[2];
    }
}