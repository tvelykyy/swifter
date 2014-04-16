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

        $jsonBlocks = $this->serializationService->serializeToJsonByGroup($blocks, 'list');

        return $this->responseService->generateJsonResponse($jsonBlocks, Response::HTTP_OK);
    }

    public function saveBlockAction()
    {
        $block = $this->serializationService->deserializeFromJson($this->get('request')->getContent(), self::BLOCK_CLASS);

        $errors = $this->validate($block);

        if (count($errors) > 0) {
            $response = $this->responseService->generateErrorsJsonResponse($errors);
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