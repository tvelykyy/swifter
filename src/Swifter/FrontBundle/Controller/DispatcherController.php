<?php

namespace Swifter\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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

    public function indexAction($uri)
    {
        $slashLeadedUri = $this->leadWithSlash($uri);
        $page = $this->getDoctrine()
            ->getRepository('SwifterFrontBundle:Page')
            ->findOneByUri($slashLeadedUri);

        if (!$page) {
            throw $this->createNotFoundException('Page not found.');

        }

        $this->mergeWithParentPageBlocks($page);

        $this->snippetService->resolveSnippetsForPage($page);

        $blocks = $this->convertPageBlocksToAssociativeArray($page->getPageBlocks());

        return $this->render('SwifterFrontBundle:DevTest:index.html.twig', $blocks);
    }

    private function leadWithSlash($uri)
    {
        return '/'.$uri;
    }

    private function mergeWithParentPageBlocks($page)
    {
        $currentPageBlocks = $page->getPageBlocks();
        $parent = $page->getParent();

        do {
            if (isset($parent)) {
                $blocksToAdd = $parent->getPageBlocks()->filter(
                    function($pageBlock) use ($currentPageBlocks) {
                        return !$currentPageBlocks->exists(
                            function($index, $currentPageBlock) use ($pageBlock) {
                                return $currentPageBlock->getBlock()->getTitle() === $pageBlock->getBlock()->getTitle();
                            }
                        );
                    }
                );

                $currentPageBlocks = new ArrayCollection(
                    array_merge($currentPageBlocks->toArray(), $blocksToAdd->toArray())
                );
            }
            $parent = $parent->getParent();
        } while (isset($parent));

        $page->setPageBlocks($currentPageBlocks);
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
