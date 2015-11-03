<?php

namespace Swifter\CommonBundle\Tests\Service;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Swifter\CommonBundle\Service\PageBlockService;
use Swifter\CommonBundle\Service\PageService;

class PageBlockServiceTest extends WebTestCase
{
    private $pageBlockService;
    private $fixtures;
    private $em;

    public function __construct()
    {
        $this->em = $this->getContainer()->get('doctrine')->getEntityManager();
        $this->pageBlockService = new PageBlockService($this->em);
    }

    protected function setUp()
    {
        parent::setUp();

        $classes = [
            'Swifter\CommonBundle\DataFixtures\Test\LoadPagesData'
        ];
        $this->fixtures = $this->loadFixtures($classes)->getReferenceRepository();
    }

    public function testDeleteForPageOtherBlocksThan()
    {
        /* Given. */
        $blockToDelete = $this->fixtures->getReference('main-page-main-content-block');
        $blockNotToDelete = $this->fixtures->getReference('main-page-title-block');

        /* When. */
        $deletedCount = $this->pageBlockService->deleteForPageOtherBlocksThan($blockNotToDelete->getPage()->getId(), [$blockNotToDelete->getId()]);
        $notDeletedBlock = $this->em->getRepository('SwifterCommonBundle:PageBlock')->find($blockNotToDelete->getId());
        $deletedBlock = $this->em->getRepository('SwifterCommonBundle:PageBlock')->find($blockToDelete->getId());

        /* Then. */
        $this->assertEquals(1, $deletedCount);
        $this->assertNotNull($notDeletedBlock);
        $this->assertNotNull($deletedBlock);
    }

}