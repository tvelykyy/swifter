<?php

namespace Swifter\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\SerializationContext;

abstract class CrudController extends Controller
{
    protected function createObjectAndReturn201Response($object)
    {
        $em = $this->getEM();

        $em->persist($object);
        $em->flush();

        $responseBody = $object->getId();

        return Response::create($responseBody, Response::HTTP_CREATED);
    }

    protected function editObjectAndReturn204Response($object)
    {
        $em = $this->getEM();

        $em->merge($object);
        $em->flush();

        return Response::create('', Response::HTTP_NO_CONTENT);
    }

    protected function deleteObjectAndReturn204Response($object)
    {
        $em = $this->getEM();

        $em->remove($object);
        $em->flush();

        return Response::create('', Response::HTTP_NO_CONTENT);
    }

    protected function generate200JsonResponse($jsonResponseBody)
    {
        $response = Response::create($jsonResponseBody, Response::HTTP_OK);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    protected function serializeToJsonObjectByGroup($object, $serializationGroup)
    {
        $serializationContext = SerializationContext::create()->setGroups(array($serializationGroup));
        return $this->serializeToJsonObjectByContext($object, $serializationContext);
    }

    protected function serializeToJsonObjectByContext($object, $serializationContext)
    {
        $serializer = $this->container->get('serializer');
        $json = $serializer->serialize($object, 'json', $serializationContext);

        return $json;
    }

    protected function deserializeObjectFromRequest()
    {
        $requestBody = $this->get('request')->getContent();
        $object = $this->deserializeObjectFromJson($requestBody, 'Swifter\CommonBundle\Entity\Block');

        return $object;
    }

    protected function deserializeObjectFromJson($json, $className)
    {
        $serializer = $this->container->get('serializer');
        $object = $serializer->deserialize($json, $className, 'json');

        return $object;
    }

    protected function getEM()
    {
        $em = $this->getDoctrine()->getManager();
        return $em;
    }

}