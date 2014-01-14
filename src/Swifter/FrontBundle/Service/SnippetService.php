<?php

namespace Swifter\FrontBundle\Service;

use Swifter\FrontBundle\Entity\Page;
use Swifter\FrontBundle\Entity\PageBlock;
use Swifter\FrontBundle\Entity\Snippet;
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

    public function resolveSnippetsForPage(Page $page)
    {
        foreach($page->getPageBlocks()->toArray() as $pageBlock)
        {
            $this->resolveSnippetsForPageBlock($pageBlock);
        }
    }

    private function resolveSnippetsForPageBlock(PageBlock $pageBlock)
    {
        $snippetsTitles = $this->getSnippetsFromPageBlockContent($pageBlock);

        foreach($snippetsTitles as $snippetTitle)
        {
            $snippet = $this->em->getRepository('SwifterFrontBundle:Snippet')
                ->findOneByTitle($snippetTitle);
            $html = $this->resolveSnippet($snippet);

            $pageBlock->setContent(preg_replace(
                '/\\[\\['.$snippetTitle.'\\]\\]/',
                $html,
                $pageBlock->getContent())
            );
        }
    }

    private function getSnippetsFromPageBlockContent(PageBlock $pageBlock)
    {
        $matchedSnippets = null;
        preg_match_all('/\\[\\[(.*?)\\]\\]/', $pageBlock->getContent(), $matchedSnippets);

        $snippetsTitles = array();

        /* $snippets[0] returns match like this [[(.*?)]], we need (.*?)., so we take [1] */
        foreach ($matchedSnippets[1] as $matchedSnippet)
        {
            $snippetsTitles[] = $matchedSnippet;
        }

        return $snippetsTitles;
    }

    private function resolveSnippet(Snippet $snippet)
    {
        $executionResult = $this->executeSnippet($snippet);
        $html = $this->render($snippet->getTemplate()->getPath(), $executionResult);

        return $html;
    }

    private function executeSnippet(Snippet $snippet)
    {
        $service = $this->container->get($snippet->getService());
        $paramsArray = json_decode($snippet->getParams(), true);
        $result = call_user_func_array(array($service, $snippet->getMethod()), $paramsArray);

        return $result;
    }

    private function render($template, $params)
    {
        return $this->container->get('templating')->render($template, array('params' => $params));
    }
}