<?php

namespace Swifter\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

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

        return new JsonResponse($blocks);
    }
}