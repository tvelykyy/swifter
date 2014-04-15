<?php

namespace Swifter\AdminBundle\Controller;

use Swifter\AdminBundle\Service\ResponseService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\SerializationContext;

abstract class CrudController extends Controller
{
    protected $responseService;
    protected $serializationService;

    public function __construct(ResponseService $responseService)
    {
        $this->responseService = $responseService;
    }

    protected function saveAndGenerateResponse($entity)
    {
        if ($entity->getId() == null) {
            $response = $this->createAndGenerate201Response($entity);
        } else {
            $response = $this->editAndGenerate204Response($entity);
        }

        return $response;
    }

    protected function createAndGenerate201Response($entity)
    {
        $this->doWithEntity('persist', $entity);
        $responseBody = $entity->getId();

        return $this->responseService->generateJsonResponse($responseBody, Response::HTTP_CREATED);
    }

    protected function editAndGenerate204Response($entity)
    {
        $this->doWithEntity('merge', $entity);

        return $this->responseService->generateEmptyResponse(Response::HTTP_NO_CONTENT);
    }

    protected function deleteAndReturn204Response($entity)
    {
        $this->doWithEntity('remove', $entity);

        return $this->responseService->generateEmptyResponse(Response::HTTP_NO_CONTENT);
    }

    protected function doWithEntity($method, $entity)
    {
        $em = $this->getDoctrine()->getManager();

        call_user_func(array($em, $method), $entity);
        $em->flush();
    }

    protected function validate($entity)
    {
        $validator = $this->get('validator');
        $errors = $validator->validate($entity);

        return $errors;
    }

}