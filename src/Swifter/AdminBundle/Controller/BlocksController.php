<?php

namespace Swifter\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class BlocksController extends CrudController
{
    public function renderManagePageAction()
    {
        return $this->render('SwifterAdminBundle::blocks.html.twig', array('title' => 'Blocks Management'));
    }

    public function retrieveBlocksAction()
    {
        $blocks = $this->getDoctrine()
            ->getRepository('SwifterCommonBundle:Block')
            ->findAll();

        return $this->generate200JsonResponseWithBody($blocks);
    }

    public function saveBlockAction()
    {
        $block = $this->deserializeObjectFromRequest();

        if ($block->getId() == null)
        {
            return $this->createObjectAndReturn201Response($block);
        }
        else
        {
            return $this->editObjectAndReturn204Response($block);
        }
    }

    public function deleteBlockAction($id)
    {
        $blockToDelete = $this->getDoctrine()
            ->getRepository('SwifterCommonBundle:Block')
            ->find($id);

        return $this->deleteObjectAndReturn204Response($blockToDelete);
    }

}