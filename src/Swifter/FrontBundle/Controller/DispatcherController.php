<?php

namespace Swifter\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Swifter\FrontBundle\Service\SnippetService;
use Doctrine\Common\Collections\ArrayCollection;

class DispatcherController extends Controller
{
    protected $twig;
    protected $snippetService;

    public function __construct(\Twig_Environment $twig, SnippetService $snippetService)
    {
        $this->twig = $twig;
        $this->snippetService = $snippetService;
    }

    public function indexAction($uri, Request $request)
    {
        $slashLeadedUri = $this->leadWithSlash($uri);
        $page = $this->getDoctrine()
            ->getRepository('SwifterFrontBundle:Page')
            ->findOneByUri($slashLeadedUri);

        if (!$page) {
            throw $this->createNotFoundException('Page not found.');
        }

        $this->mergePageBlocksWithParents($page);
        $queryParams = $request->query->all();
        $this->snippetService->resolveSnippetsForPage($page, $queryParams);
        $blocks = $this->convertPageBlocksToAssociativeArray($page->getPageBlocks());

        return $this->render($page->getTemplate()->getPath(), $blocks);
    }

    private function leadWithSlash($uri)
    {
        return '/'.$uri;
    }

    private function mergePageBlocksWithParents($page)
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

    private function convertPageBlocksToAssociativeArray($pageBlocks)
    {
        $blockValue = array();
        foreach($pageBlocks->toArray() as $pageBlock)
        {
            $blockValue[$pageBlock->getBlock()->getTitle()] = $pageBlock->getContent();
        }

        return $blockValue;
    }

}
