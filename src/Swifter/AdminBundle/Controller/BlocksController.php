<?php

namespace Swifter\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class BlocksController extends Controller
{
    public function renderManagePageAction()
    {
        return $this->render('SwifterAdminBundle::blocks.html.twig');
    }

    public function retrieveBlocksAction()
    {
        $blocks = $this->getDoctrine()
            ->getRepository('SwifterCommonBundle:Block')
            ->findAll();

        $serializer = $this->container->get('serializer');
        $blocksInJson = $serializer->serialize($blocks, 'json');

        $response = new Response($blocksInJson);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    public function saveBlockAction(Request $request)
    {
        $postBody = $this->get("request")->getContent();
        if (!empty($postBody))
        {
            $serializer = $this->container->get('serializer');
            $block = $serializer->deserialize($postBody, 'Swifter\CommonBundle\Entity\Block', 'json');

            $em = $this->getDoctrine()->getManager();

            $responseBody = null;
            $responseStatus = null;

            if ($block->getId() == null)
            {
                $em->persist($block);
                $em->flush();
                $responseBody = $block->getId();
                $responseStatus = Response::HTTP_CREATED;
            }
            else
            {
                $em->merge($block);
                $responseStatus = Response::HTTP_NO_CONTENT;
            }
            $em->flush();

            return new Response($responseBody, $responseStatus);
        }
    }

    public function deleteBlockAction($id)
    {
        $blockToDelete = $this->getDoctrine()
            ->getRepository('SwifterCommonBundle:Block')
            ->find($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($blockToDelete);

        $em->flush();

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}