<?php

namespace Swifter\AdminBundle\Service;

use Symfony\Component\HttpFoundation\Response;

class ResponseService
{
    public function generateErrorsJsonResponse($errors)
    {
        $errorArray = array();
        foreach ($errors as $error) {
            $errorArray[] = (object)array('field' => $error->getPropertyPath(), 'message' => $error->getMessage());
        }

        return $this->generateJsonResponse(json_encode($errorArray), Response::HTTP_BAD_REQUEST);
    }

    public function generateJsonResponse($jsonBody, $status = Response::HTTP_OK, $headers = array())
    {
        $response = $this->generateResponse($jsonBody, $status, $headers);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    public function generateEmptyResponse($status, $headers = array())
    {
        return $this->generateResponse('', $status, $headers);
    }

    public function generateResponse($body, $status = Response::HTTP_OK, $headers = array())
    {
        return Response::create($body, $status, $headers);
    }
}