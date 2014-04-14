<?php

namespace Swifter\AdminBundle\Tests\Service;

use Swifter\AdminBundle\Service\ResponseService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolation;

class ResponseServiceTest extends \PHPUnit_Framework_TestCase
{
    protected $responseService;

    const SIMPLE_BODY = '{"id"=1}';

    const CONTENT_TYPE_HEADER_NAME = 'Content-Type';
    const CONTENT_TYPE_HEADER_VALUE_JSON = 'application/json';

    public function __construct()
    {
        $this->responseService = new ResponseService();
    }
    public function testShouldGenerateResponseByAllParams()
    {
        /* Given. */
        $body = self::SIMPLE_BODY;
        $status = Response::HTTP_BAD_REQUEST;
        $headers = array(self::CONTENT_TYPE_HEADER_NAME => self::CONTENT_TYPE_HEADER_VALUE_JSON);

        /* When. */
        $response = $this->responseService->generateResponse($body, $status, $headers);

        /* Then. */
        $this->assertEquals($body, $response->getContent());
        $this->assertEquals($status, $response->getStatusCode());
        $this->assertEquals(self::CONTENT_TYPE_HEADER_VALUE_JSON, $response->headers->get(self::CONTENT_TYPE_HEADER_NAME));
    }

    public function testShouldGenerateEmptyResponse()
    {
        /* Given. */
        $status = Response::HTTP_BAD_REQUEST;

        /* When. */
        $response = $this->responseService->generateEmptyResponse($status);

        /* Then. */
        $this->assertEquals('', $response->getContent());
    }

    public function testShouldGenerateResponseWithJsonContentType()
    {
        /* Given. */
        $body = self::SIMPLE_BODY;

        /* When. */
        $response = $this->responseService->generateJsonResponse($body);

        /* Then. */
        $this->assertEquals(self::CONTENT_TYPE_HEADER_VALUE_JSON, $response->headers->get(self::CONTENT_TYPE_HEADER_NAME));
    }

    public function testShouldGenerateJsonResponseWithErrors()
    {
        /* Given. */
        $messageKey = 'message';
        $fieldKey = 'field';
        $errorMessage1 = 'error1';
        $errorMessage2 = 'error2';
        $errorField1 = 'field1';
        $errorField2 = 'field2';

        $error1 = new ConstraintViolation($errorMessage1, null, array(), null, $errorField1, null);
        $error2 = new ConstraintViolation($errorMessage2, null, array(), null, $errorField2, null);
        $errors = array($error1, $error2);

        /* When. */
        $response = $this->responseService->generateErrorsJsonResponse($errors);

        /* Then. */
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals(self::CONTENT_TYPE_HEADER_VALUE_JSON, $response->headers->get(self::CONTENT_TYPE_HEADER_NAME));
        $jsonDecodedBody = json_decode($response->getContent());
        $this->assertTrue(is_array($jsonDecodedBody));
        $this->assertEquals(2, count($jsonDecodedBody));

        $this->assertEquals($errorMessage1, $jsonDecodedBody[0]->$messageKey);
        $this->assertEquals($errorField1, $jsonDecodedBody[0]->$fieldKey);
        $this->assertEquals($errorMessage2, $jsonDecodedBody[1]->$messageKey);
        $this->assertEquals($errorField2, $jsonDecodedBody[1]->$fieldKey);
    }
}