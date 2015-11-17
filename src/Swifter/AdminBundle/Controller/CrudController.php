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

    protected function create($className)
    {
        return $this->doWithClassName('createAndGenerateResponse', $className);
    }

    protected function edit($className)
    {
        return $this->doWithClassName('editAndGenerateResponse', $className);
    }

    private function doWithClassName($operation, $className)
    {
        $entity = $this->serializationService->deserializeFromJson($this->get('request')->getContent(), $className);

        $errors = $this->validate($entity);

        if (count($errors) > 0) {
            $response = $this->responseService->generateErrorsJsonResponse($errors);
        }
        else
        {
            $response = $this->crudService->$operation($entity);
        }

        return $response;
    }

}