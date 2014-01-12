<?php

namespace Swifter\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Swifter\FrontBundle\Service\SnippetService;

class DispatcherController extends Controller
{
    protected $snippetService;

    public function __construct(SnippetService $snippetService)
    {
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

        $this->snippetService->resolveSnippetsForPage($page);

        $blocks = $this->convertPageBlocksToAssociativeArray($page->getPageBlocks());

        return $this->render('SwifterFrontBundle:Default:index.html.twig', $blocks);
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
