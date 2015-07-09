<?php

namespace Swifter\AdminBundle\Controller;

use Doctrine\Common\Collections\Criteria;
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

        return $this->responseService->generateJsonResponse($jsonBlocks);
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

        return $this->deleteAndGenerate204Response($blockToDelete);
    }

    public function getBlocksByTitlesAction($semicolonSeparatedTitles)
    {
        $titles = explode(';', $semicolonSeparatedTitles);

        $qb = $this->getDoctrine()
            ->getRepository(static::BLOCK_CLASS_BUNDLE_PREFIX)->createQueryBuilder('b');

        $blocks = $qb->where($qb->expr()->in('b.title', $titles))
            ->getQuery()
            ->getResult();

        $jsonBlocks = $this->serializationService->serializeToJsonByGroup($blocks, 'list');

        return $this->responseService->generateJsonResponse($jsonBlocks);
    }

}