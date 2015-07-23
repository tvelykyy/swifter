<?php

namespace Swifter\AdminBundle\Tests\Service;

use Swifter\AdminBundle\Service\CrudService;
use Symfony\Component\HttpFoundation\Response;

class CrudServiceTest extends \PHPUnit_Framework_TestCase
{
    private $crudService;
    private $responseServiceMock;
    private $emMock;

    public function setUp()
    {
        $this->responseServiceMock = $this->getMock('Swifter\AdminBundle\Service\ResponseService');
        $this->emMock = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->crudService = new CrudService($this->responseServiceMock, $this->emMock);
    }

    public function testShouldCreateAndGenerateResponse()
    {
        /* Given. */
        $id = 1;
        $entity = new Entity();

        $this->emMock->expects($this->once())
            ->method('persist')
            ->with($this->identicalTo($entity))
            ->will($this->returnCallback(function($p) use($id) { $p->setId($id);}));

        $this->responseServiceMock->expects($this->once())
            ->method('generateJsonResponse')
            ->with(
                $this->equalTo($id),
                $this->equalTo(Response::HTTP_CREATED)
            );

        /* When. */
        $response = $this->crudService->createAndGenerate201Response($entity);

        /* Then. */
    }

    public function testShouldEditAndGenerateResponse()
    {
        /* Given. */
        $entity = new Entity(1);

        $this->emMock->expects($this->once())
            ->method('merge')
            ->with($this->identicalTo($entity));

        $this->responseServiceMock->expects($this->once())
            ->method('generateEmptyResponse')
            ->with($this->equalTo(Response::HTTP_NO_CONTENT));

        /* When. */
        $response = $this->crudService->editAndGenerate204Response($entity);

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
        $response = $this->crudService->deleteAndGenerate204Response($entity);

        /* Then. */
    }

    public function testShouldSaveNewAndGenerateResponse()
    {
        /* Given. */
        $entity = new Entity();

        $partiallyMockedCrudService = $this->getMockBuilder('Swifter\AdminBundle\Service\CrudService')
            ->disableOriginalConstructor()
            ->setMethods(array('createAndGenerate201Response', 'editAndGenerate204Response'))
            ->getMock();

        $partiallyMockedCrudService->expects($this->once())
            ->method('createAndGenerate201Response');

        /* When. */
        $response = $partiallyMockedCrudService->saveAndGenerateResponse($entity);


        /* Then. */
    }

    public function testShouldSaveExistingAndGenerateResponse()
    {
        /* Given. */
        $entity = new Entity(1);

        $partiallyMockedCrudService = $this->initPartiallyMockedCrudService();

        $partiallyMockedCrudService->expects($this->once())
            ->method('editAndGenerate204Response');

        /* When. */
        $response = $partiallyMockedCrudService->saveAndGenerateResponse($entity);


        /* Then. */
    }

    private function initPartiallyMockedCrudService()
    {
        $partiallyMockedCrudService = $this->getMockBuilder('Swifter\AdminBundle\Service\CrudService')
            ->disableOriginalConstructor()
            ->setMethods(array('createAndGenerate201Response', 'editAndGenerate204Response'))
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