<?php

namespace Swifter\AdminBundle\Service;

use Doctrine\ORM\EntityManager;
use Swifter\CommonBundle\Entity\Serialization\SerializationGroups;
use Symfony\Component\HttpFoundation\Response;

class CrudService
{
    protected $em;
    protected $responseService;
    protected $serializationService;

    public function __construct(ResponseService $responseService, SerializationService $serializationService, EntityManager $em)
    {
        $this->responseService = $responseService;
        $this->serializationService = $serializationService;
        $this->em = $em;
    }

    public function createAndGenerateResponse($entity)
    {
        $created = $this->doWithEntity('merge', $entity);
        $responseBody = $this->serializationService->serializeToJsonByGroup($created, SerializationGroups::DETAILS_GROUP);

        return $this->responseService->generateJsonResponse($responseBody, Response::HTTP_CREATED);
    }

    public function editAndGenerateResponse($entity)
    {
        $updated = $this->doWithEntity('merge', $entity);
        $responseBody = $this->serializationService->serializeToJsonByGroup($updated, SerializationGroups::DETAILS_GROUP);

        return $this->responseService->generateJsonResponse($responseBody, Response::HTTP_OK);
    }

    public function deleteAndGenerateResponse($entity)
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