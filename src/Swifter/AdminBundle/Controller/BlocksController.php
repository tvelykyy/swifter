<?php

namespace Swifter\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

class BlocksController extends CrudController
{
    const BLOCK_CLASS_BUNDLE_PREFIX = 'SwifterCommonBundle:Block';
    const BLOCK_CLASS = 'Swifter\CommonBundle\Entity\Block';

    public function renderManagePageAction()
    {
        return $this->render('SwifterAdminBundle::blocks.html.twig', array('title' => 'Blocks Management'));
    }

    public function retrieveBlocksAction()
    {
        $blocks = $this->getDoctrine()
            ->getRepository(self::BLOCK_CLASS_BUNDLE_PREFIX)
            ->findAll();

        $jsonBlocks = $this->serializeToJsonByGroup($blocks, 'list');

        return $this->generateJsonResponse($jsonBlocks, Response::HTTP_OK);
    }

    public function saveBlockAction()
    {
        $block = $this->deserializeFromRequest(self::BLOCK_CLASS);

        $errors = $this->validate($block);

        if (count($errors) > 0) {
            $response = $this->generateErrorsJsonResponse($errors);
        }
        else
        {
            $response = $this->saveAndGenerateResponse($block);
        }

        return $response;
    }

    public function deleteBlockAction($id)
    {
        $blockToDelete = $this->getDoctrine()
            ->getRepository(self::BLOCK_CLASS_BUNDLE_PREFIX)
            ->find($id);

        return $this->deleteAndReturn204Response($blockToDelete);
    }

}