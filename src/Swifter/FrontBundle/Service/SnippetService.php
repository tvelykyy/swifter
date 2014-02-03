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
    protected $container;
    protected $em;

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
        $snippetsTitles = $this->getSnippetsTitlesFromPageBlockContent($pageBlock);

        foreach($snippetsTitles as $snippetTitle)
        {
            $snippet = $this->getSnippetFromRepository($snippetTitle);
            $html = $this->resolveSnippet($snippet, $queryParams);
            $this->updatePageBlockContentWithResolvedSnippet($pageBlock, $snippetTitle, $html);
        }
    }

    private function getSnippetsTitlesFromPageBlockContent(PageBlock $pageBlock)
    {
        $matchedSnippets = null;
        preg_match_all('/\\[\\[([A-Za-z0-9_]*?)\\]\\]/', $pageBlock->getContent(), $matchedSnippets);

        $snippetsTitles = array();

        /* $snippets[0] returns match like this [[(.*?)]], we need (.*?), so we take [1] */
        foreach ($matchedSnippets[1] as $matchedSnippet)
        {
            $snippetsTitles[] = $matchedSnippet;
        }

        return $snippetsTitles;
    }

    private function getSnippetFromRepository($snippetTitle)
    {
        $snippet = $this->em->getRepository('SwifterCommonBundle:Snippet')
            ->findOneByTitle($snippetTitle);
        return $snippet;
    }

    private function resolveSnippet(Snippet $snippet, $queryParams)
    {
        $executionResult = $this->executeSnippet($snippet, $queryParams);
        $html = $this->render($snippet->getTemplate()->getPath(), $executionResult);

        return $html;
    }

    private function executeSnippet(Snippet $snippet, $queryParams)
    {
        $mergedParams = $this->mergeConfiguredAndQueryParams($snippet, $queryParams);
        $service = $this->container->get($snippet->getService());

        $result = call_user_func_array(array($service, $snippet->getMethod()), $mergedParams);

        return $result;
    }

    private function mergeConfiguredAndQueryParams(Snippet $snippet, $queryParams)
    {
        $configuredParams = json_decode($snippet->getParams(), true);
        $mergedParams = array_merge($configuredParams, $queryParams);

        return $mergedParams;
    }

    private function render($templatePath, $params)
    {
        return $this->container->get('templating')->render($templatePath, array('params' => $params));
    }

    private function updatePageBlockContentWithResolvedSnippet(PageBlock $pageBlock, $snippetTitle, $html)
    {
        $pageBlock->setContent(preg_replace(
                '/\\[\\[' . $snippetTitle . '\\]\\]/',
                $html,
                $pageBlock->getContent())
        );
    }


}
