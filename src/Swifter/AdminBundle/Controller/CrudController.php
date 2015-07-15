<?php

namespace Swifter\AdminBundle\Controller;

use Swifter\AdminBundle\Service\ResponseService;
use Swifter\AdminBundle\Service\CrudService;
use Swifter\AdminBundle\Service\SerializationService;

abstract class CrudController extends BaseController
{
    protected $serializationService;
    protected $crudService;

    public function __construct(CrudService $crudService, ResponseService $responseService, SerializationService $serializationService)
    {
        parent::__construct($responseService);
        $this->crudService = $crudService;
        $this->serializationService = $serializationService;
    }

    protected function saveAndGenerateResponse($entity)
    {
        return $this->crudService->saveAndGenerateResponse($entity);
    }

    protected function deleteAndGenerate204Response($entity)
    {
        return $this->crudService->deleteAndGenerate204Response($entity);
    }

}