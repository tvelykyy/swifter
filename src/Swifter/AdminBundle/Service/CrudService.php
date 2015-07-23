<?php

namespace Swifter\AdminBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Response;

class CrudService
{
    private $em;
    private $responseService;

    public function __construct(ResponseService $responseService, EntityManager $em)
    {
        $this->responseService = $responseService;
        $this->em = $em;
    }

    public function saveAndGenerateResponse($entity)
    {
        if ($entity->getId() == null) {
            $response = $this->createAndGenerate201Response($entity);
        } else {
            $response = $this->editAndGenerate204Response($entity);
        }

        return $response;
    }

    public function createAndGenerate201Response($entity)
    {
        $this->doWithEntity('persist', $entity);
        $responseBody = $entity->getId();

        return $this->responseService->generateJsonResponse($responseBody, Response::HTTP_CREATED);
    }

    public function editAndGenerate204Response($entity)
    {
        $this->doWithEntity('merge', $entity);

        return $this->responseService->generateEmptyResponse(Response::HTTP_NO_CONTENT);
    }

    public function deleteAndGenerate204Response($entity)
    {
        $this->doWithEntity('remove', $entity);

        return $this->responseService->generateEmptyResponse(Response::HTTP_NO_CONTENT);
    }

    private function doWithEntity($method, $entity)
    {
        call_user_func(array($this->em, $method), $entity);
        $this->em->flush();
    }
}