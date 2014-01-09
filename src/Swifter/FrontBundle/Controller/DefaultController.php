<?php

namespace Swifter\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($uri)
    {
        $slashLeadedUri = $this->leadWithSlash($uri);
        $page = $this->getDoctrine()
            ->getRepository('SwifterFrontBundle:Page')
            ->findOneByUri($slashLeadedUri);

        if (!$page) {
            throw $this->createNotFoundException('Page not found.');
        }

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
