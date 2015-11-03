<?php

namespace Swifter\AdminBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Response;

class CrudService
{
    protected $em;
    protected $responseService;

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
        $created = $this->doWithEntity('merge', $entity);
        $responseBody = $created->getId();

        return $this->responseService->generateJsonResponse($responseBody, Response::HTTP_CREATED);
    }

    public function editAndGenerate204Response($page)
    {
        $this->doWithEntity('merge', $page);

        return $this->responseService->generateEmptyResponse(Response::HTTP_NO_CONTENT);
    }

    public function deleteAndGenerate204Response($entity)
    {
        $this->doWithEntity('remove', $entity);

        return $this->responseService->generateEmptyResponse(Response::HTTP_NO_CONTENT);
    }

    protected function doWithEntity($method, $entity)
    {
        $result = call_user_func(array($this->em, $method), $entity);
        $this->em->flush();

        return $result;
    }
}