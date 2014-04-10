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

        $blocksInJson = $this->serializeToJsonObjectByGroup($blocks, 'list');

        return $this->generateJsonResponse($blocksInJson, Response::HTTP_OK);
    }

    public function saveBlockAction()
    {
        $block = $this->deserializeObjectFromRequest('Swifter\CommonBundle\Entity\Block');

        $validator = $this->get('validator');
        $errors = $validator->validate($block);

        if (count($errors) > 0) {
            $errorArray = [];
            foreach($errors as $error )
            {
                $errorArray[] = (object)array('field' => $error->getPropertyPath(), 'message' => $error->getMessage());
            }

            return $this->generateJsonResponse(json_encode($errorArray), Response::HTTP_BAD_REQUEST);
        }
        else
        {
            if ($block->getId() == null)
            {
                return $this->createObjectAndReturn201Response($block);
            }
            else
            {
                return $this->editObjectAndReturn204Response($block);
            }
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