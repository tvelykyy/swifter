<?php

namespace Swifter\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\SerializationContext;

abstract class CrudController extends Controller
{
    protected function saveAndGenerateResponse($block)
    {
        if ($block->getId() == null) {
            $response = $this->createAndGenerate201Response($block);
        } else {
            $response = $this->editAndGenerate204Response($block);
        }

        return $response;
    }

    protected function createAndGenerate201Response($object)
    {
        $em = $this->getEM();

        $em->persist($object);
        $em->flush();

        $responseBody = $object->getId();

        return Response::create($responseBody, Response::HTTP_CREATED);
    }

    protected function editAndGenerate204Response($object)
    {
        $em = $this->getEM();

        $em->merge($object);
        $em->flush();

        return $this->generateEmptyResponse(Response::HTTP_NO_CONTENT);
    }

    protected function deleteAndReturn204Response($object)
    {
        $em = $this->getEM();

        $em->remove($object);
        $em->flush();

        return $this->generateEmptyResponse(Response::HTTP_NO_CONTENT);
    }

    protected function generateErrorsJsonResponse($errors)
    {
        $errorArray = array();
        foreach ($errors as $error) {
            $errorArray[] = (object)array('field' => $error->getPropertyPath(), 'message' => $error->getMessage());
        }

        return $this->generateJsonResponse(json_encode($errorArray), Response::HTTP_BAD_REQUEST);
    }

    protected function generateJsonResponse($jsonBody, $status = Response::HTTP_OK, $headers = array())
    {
        $response = $this->generateResponse($jsonBody, $status, $headers);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    protected function generateEmptyResponse($status, $headers = array())
    {
        return $this->generateResponse('', $status, $headers);
    }

    protected function generateResponse($body, $status = Response::HTTP_OK, $headers = array())
    {
        return Response::create($body, $status, $headers);
    }

    protected function serializeToJsonByGroup($object, $serializationGroup)
    {
        $serializationContext = SerializationContext::create()->setGroups(array($serializationGroup));
        return $this->serializeToJsonByContext($object, $serializationContext);
    }

    protected function serializeToJsonByContext($object, $serializationContext)
    {
        $serializer = $this->container->get('serializer');
        $json = $serializer->serialize($object, 'json', $serializationContext);

        return $json;
    }

    protected function deserializeFromRequest($className)
    {
        $requestBody = $this->get('request')->getContent();
        $object = $this->deserializeFromJson($requestBody, $className);

        return $object;
    }

    protected function deserializeFromJson($json, $className)
    {
        $serializer = $this->container->get('serializer');
        $object = $serializer->deserialize($json, $className, 'json');

        return $object;
    }

    protected function validate($object)
    {
        $validator = $this->get('validator');
        $errors = $validator->validate($object);

        return $errors;
    }

    protected function getEM()
    {
        $em = $this->getDoctrine()->getManager();
        return $em;
    }

}