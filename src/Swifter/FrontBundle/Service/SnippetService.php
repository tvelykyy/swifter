<?php

namespace Swifter\FrontBundle\Service;

use Swifter\CommonBundle\Entity\Page;
use Swifter\CommonBundle\Entity\PageBlock;
use Swifter\CommonBundle\Entity\Snippet;
use Swifter\FrontBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

use Doctrine\ORM\EntityManager;

class SnippetService
{
    private $container;
    private $em;

    public function __construct(Container $container, EntityManager $em)
    {
        $this->container = $container;
        $this->em = $em;
    }

    public function resolveSnippetsForPage(Page $page, $queryParams)
    {
        foreach($page->getPageBlocks()->toArray() as $pageBlock)
        {
            $this->resolveSnippetsForPageBlock($pageBlock, $queryParams);
        }
    }

    private function resolveSnippetsForPageBlock(PageBlock $pageBlock, $queryParams)
    {
        $parsedSnippets = $this->parseSnippetsFromPageBlockContent($pageBlock);

        foreach($parsedSnippets as $snippetString => $parsedSnippet)
        {
            $snippet = $this->getSnippetFromRepository($parsedSnippet[0]);
            $html = $this->resolveSnippet($snippet, $queryParams, $parsedSnippet[1]);
            $this->updatePageBlockContentWithResolvedSnippet($pageBlock, $snippetString, $html);
        }
    }

    private function parseSnippetsFromPageBlockContent(PageBlock $pageBlock)
    {
        $snippetsStrings = null;
        preg_match_all('/\\[\\[([A-Za-z0-9_\\?=\\&]*?)\\]\\]/', $pageBlock->getContent(), $snippetsStrings);

        $snippets = array();

        /* $snippets[0] returns match like this [[(.*?)]], we need (.*?), so we take [1] */
        foreach ($snippetsStrings[1] as $snippetString)
        {
            //Snippet string is similar to url, so built-in function is used to parse snippet easily
            $snippet = parse_url($snippetString);
            $params = [];
            if (array_key_exists('query', $snippet))
            {
                parse_str($snippet['query'], $params);
            }
            $snippets[$snippetString] = [$snippet['path'], $params];
        }

        return $snippets;
    }

    private function getSnippetFromRepository($snippetTitle)
    {
        $snippet = $this->em->getRepository('SwifterCommonBundle:Snippet')
            ->findOneByTitle($snippetTitle);
        return $snippet;
    }

    private function resolveSnippet(Snippet $snippet, $defaultParams, $queryParams)
    {
        $executionResult = $this->executeSnippet($snippet, $defaultParams, $queryParams);
        $html = $this->render($snippet->getTemplate()->getPath(), $executionResult);

        return $html;
    }

    private function executeSnippet(Snippet $snippet, $queryParams, $defaultParams)
    {
        $mergedParams = $this->mergeConfiguredAndQueryParams($snippet, $queryParams, $defaultParams);
        $service = $this->container->get($snippet->getService());

        $result = call_user_func_array(array($service, $snippet->getMethod()), $mergedParams);

        return $result;
    }

    private function mergeConfiguredAndQueryParams(Snippet $snippet, $queryParams, $defaultParams)
    {
        $configuredParams = json_decode($snippet->getParams(), true);
        $mergedParams = array_merge($configuredParams, $defaultParams, $queryParams);

        return $mergedParams;
    }

    private function render($templatePath, $params)
    {
        return $this->container->get('templating')->render($templatePath, array('params' => $params));
    }

    private function updatePageBlockContentWithResolvedSnippet(PageBlock $pageBlock, $snippetString, $html)
    {
        $pageBlock->setContent(preg_replace(
                '/\\[\\[' . preg_quote($snippetString) . '\\]\\]/',
                $html,
                $pageBlock->getContent())
        );
    }


}
