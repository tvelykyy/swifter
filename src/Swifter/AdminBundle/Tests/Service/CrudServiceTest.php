<?php

namespace Swifter\AdminBundle\Tests\Service;

use Swifter\AdminBundle\Service\CrudService;
use Symfony\Component\HttpFoundation\Response;

class CrudServiceTest extends \PHPUnit_Framework_TestCase
{
    private $crudService;
    private $responseServiceMock;
    private $serializationServiceMock;
    private $emMock;

    public function setUp()
    {
        $this->responseServiceMock = $this->getMock('Swifter\AdminBundle\Service\ResponseService');
        $this->serializationServiceMock = $this->getMockBuilder('Swifter\AdminBundle\Service\SerializationService')
            ->disableOriginalConstructor()->getMock();
        $this->emMock = $this->getMockBuilder('Doctrine\ORM\EntityManager')->disableOriginalConstructor()->getMock();

        $this->crudService = new CrudService($this->responseServiceMock, $this->serializationServiceMock, $this->emMock);
    }

    public function testShouldCreateAndGenerateResponse()
    {
        /* Given. */
        $id = 1;
        $entity = new Entity();

        $this->emMock->expects($this->once())
            ->method('merge')
            ->with($this->identicalTo($entity))
            ->will($this->returnCallback(function($p) use($id) { $p->setId($id); return $p;}));

        $this->serializationServiceMock->method('serializeToJsonByGroup')
            ->willReturn($id);

        $this->responseServiceMock->expects($this->once())
            ->method('generateJsonResponse')
            ->with(
                $this->equalTo($id),
                $this->equalTo(Response::HTTP_CREATED)
            );

        /* When. */
        $response = $this->crudService->createAndGenerateResponse($entity);

        /* Then. */
    }

    public function testShouldEditAndGenerateResponse()
    {
        /* Given. */
        $entity = new Entity(1);

        $this->emMock->expects($this->once())
            ->method('merge')
            ->with($this->identicalTo($entity));

        $json = 'json';
        $this->serializationServiceMock->method('serializeToJsonByGroup')
            ->willReturn($json);

        $this->responseServiceMock->expects($this->once())
            ->method('generateJsonResponse')
            ->with(
                $this->equalTo($json),
                $this->equalTo(Response::HTTP_OK)
            );

        /* When. */
        $response = $this->crudService->editAndGenerateResponse($entity);

        /* Then. */
    }

    public function testShouldDeleteAndGenerateResponse()
    {
        /* Given. */
        $entity = new Entity(1);

        $this->emMock->expects($this->once())
            ->method('remove')
            ->with($this->identicalTo($entity));

        $this->responseServiceMock->expects($this->once())
            ->method('generateEmptyResponse')
            ->with($this->equalTo(Response::HTTP_NO_CONTENT));

        /* When. */
        $response = $this->crudService->deleteAndGenerateResponse($entity);

        /* Then. */
    }

    public function testShouldSaveExistingAndGenerateResponse()
    {
        /* Given. */
        $entity = new Entity(1);

        $partiallyMockedCrudService = $this->initPartiallyMockedCrudService();

        $partiallyMockedCrudService->expects($this->once())
            ->method('editAndGenerateResponse');

        /* When. */
        $response = $partiallyMockedCrudService->editAndGenerateResponse($entity);


        /* Then. */
    }

    private function initPartiallyMockedCrudService()
    {
        $partiallyMockedCrudService = $this->getMockBuilder('Swifter\AdminBundle\Service\CrudService')
            ->disableOriginalConstructor()
            ->setMethods(array('createAndGenerateResponse', 'editAndGenerateResponse'))
            ->getMock();
        return $partiallyMockedCrudService;
    }


}

class Entity
{
    private $id;

    public function __construct($id = null)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }
}