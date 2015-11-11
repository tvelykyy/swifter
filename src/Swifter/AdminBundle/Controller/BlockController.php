<?php

namespace Swifter\AdminBundle\Controller;

use Swifter\AdminBundle\Service\BlockService;
use Swifter\AdminBundle\Service\CrudService;
use Swifter\AdminBundle\Service\ResponseService;
use Swifter\AdminBundle\Service\SerializationService;
use Swifter\CommonBundle\Entity\Serialization\SerializationGroups;

class BlockController extends CrudController
{
    const BLOCK_CLASS = 'Swifter\CommonBundle\Entity\Block';

    private $blockService;

    public function __construct(CrudService $crudService, ResponseService $responseService, SerializationService $serializationService,
                                BlockService $blockService)
    {
        parent::__construct($crudService, $responseService, $serializationService);
        $this->blockService = $blockService;
    }

    public function renderBlocksAction()
    {
        return $this->render('SwifterAdminBundle::blocks.html.twig', array('title' => 'Blocks Management'));
    }

    public function getBlocksAction()
    {
        $blocks = $this->blockService->getAll();
        $jsonBlocks = $this->serializationService->serializeToJsonByGroup($blocks, SerializationGroups::LIST_GROUP);

        return $this->responseService->generateJsonResponse($jsonBlocks);
    }

    public function saveBlockAction()
    {
        return $this->save(self::BLOCK_CLASS);
    }

    public function deleteBlockAction($id)
    {
        $blockToDelete = $this->blockService->get($id);

        return $this->crudService->deleteAndGenerate204Response($blockToDelete);
    }

    public function getBlocksByTitlesAction($semicolonSeparatedTitles)
    {
        $titles = explode(';', $semicolonSeparatedTitles);
        $blocks = $this->blockService->getByTitles($titles);
        $jsonBlocks = $this->serializationService->serializeToJsonByGroup($blocks, SerializationGroups::LIST_GROUP);

        return $this->responseService->generateJsonResponse($jsonBlocks);
    }

}