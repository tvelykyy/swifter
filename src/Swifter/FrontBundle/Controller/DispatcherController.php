<?php

namespace Swifter\FrontBundle\Controller;

use Swifter\CommonBundle\Service\PageBlockService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Swifter\FrontBundle\Service\SnippetService;

class DispatcherController extends Controller
{
    protected $twig;
    protected $snippetService;
    protected $pageBlockService;

    public function __construct(\Twig_Environment $twig, SnippetService $snippetService, PageBlockService $pageBlockService)
    {
        $this->twig = $twig;
        $this->snippetService = $snippetService;
        $this->pageBlockService = $pageBlockService;
    }

    public function indexAction($uri, Request $request)
    {
        $slashLeadedUri = $this->leadWithSlash($uri);
        $page = $this->getDoctrine()
            ->getRepository('SwifterCommonBundle:Page')
            ->findOneByUri($slashLeadedUri);

        if (!$page) {
            throw $this->createNotFoundException('Page not found.');
        }

        $this->pageBlockService->mergePageBlocksWithParents($page);
        $queryParams = $request->query->all();
        $this->snippetService->resolveSnippetsForPage($page, $queryParams);
        $blocks = $this->convertPageBlocksToAssociativeArray($page->getPageBlocks());

        return $this->render($page->getTemplate()->getPath(), $blocks);
    }

    private function leadWithSlash($uri)
    {
        return '/'.$uri;
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
