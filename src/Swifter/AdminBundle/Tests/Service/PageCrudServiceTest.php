<?php

namespace Swifter\AdminBundle\Tests\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Swifter\AdminBundle\Service\PageCrudService;

class PageCrudServiceTest extends \PHPUnit_Framework_TestCase
{
    private $pageCrudService;
    private $pageBlockServiceMock;
    private $responseServiceMock;
    private $emMock;

    public function setUp()
    {
        $this->pageBlockServiceMock = $this->getMockBuilder('Swifter\CommonBundle\Service\PageBlockService')
            ->disableOriginalConstructor()
            ->getMock();;
        $this->responseServiceMock = $this->getMock('Swifter\AdminBundle\Service\ResponseService');
        $this->emMock = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->pageCrudService = new PageCrudService($this->responseServiceMock, $this->pageBlockServiceMock, $this->emMock);
    }

    public function testShouldEditAndGenerateResponse()
    {
        /* Given. */
        $pageBlock1 = new PageBlockFixture(1);
        $pageBlock2 = new PageBlockFixture(2);
        $pageBlock3 = new PageBlockFixture();
        $page = new PageFixture(1, [$pageBlock1, $pageBlock2, $pageBlock3]);

        $this->pageBlockServiceMock->expects($this->once())
            ->method('deleteForPageOtherBlocksThan')
            ->with($page->getId(), [$pageBlock1->getId(), $pageBlock2->getId()]);

        /* When. */
        $response = $this->pageCrudService->editAndGenerate204Response($page);

        /* Then. */
    }

}

class PageFixture
{
    private $id;
    private $pageBlocks;

    public function __construct($id = null, $pageBlocks = null)
    {
        $this->id = $id;
        $this->pageBlocks =  new ArrayCollection($pageBlocks);
    }

    public function getId()
    {
        return $this->id;
    }


    public function getPageBlocks()
    {
        return $this->pageBlocks;
    }
}

class PageBlockFixture
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
}