<?php

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Swifter\AdminBundle\Service\BlockService;
use Swifter\CommonBundle\DataFixtures\Test\PagesFixtures;

class BlockServiceTest extends WebTestCase
{
    private $blockService;
    private $fixtures;

    public function __construct()
    {
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $this->blockService = new BlockService($em);
    }

    protected function setUp()
    {
        parent::setUp();

        $classes = [
            'Swifter\CommonBundle\DataFixtures\Test\PagesFixtures'
        ];
        $this->fixtures = $this->loadFixtures($classes)->getReferenceRepository();
    }

    public function testGet()
    {
        /* Given. */
        $expected = $this->fixtures->getReference(PagesFixtures::MAIN_BLOCK);

        /* When. */
        $actual = $this->blockService->get($expected->getId());

        /* Then. */
        $this->assertEquals($expected->getId(), $actual->getId());
        $this->assertEquals($expected->getTitle(), $actual->getTitle());
    }

    public function testGetAll()
    {
        /* Given. */
        $block1 = $this->fixtures->getReference(PagesFixtures::MAIN_BLOCK);
        $block2 = $this->fixtures->getReference(PagesFixtures::TITLE_BLOCK);
        $block3 = $this->fixtures->getReference(PagesFixtures::FOOTER_BLOCK);

        /* When. */
        $blocks = $this->blockService->getAll();

        /* Then. */
        $this->assertEquals(3, sizeof($blocks));
        $this->assertEquals($block1->getId(), $blocks[0]->getId());
        $this->assertEquals($block2->getId(), $blocks[1]->getId());
        $this->assertEquals($block3->getId(), $blocks[2]->getId());
    }

    public function testGetByTitles()
    {
        /* Given. */
        $block1 = $this->fixtures->getReference(PagesFixtures::MAIN_BLOCK);
        $block2 = $this->fixtures->getReference(PagesFixtures::TITLE_BLOCK);
        $nonExistentBlockTitle = "nonExistentBlockTitle";
        $titles = [$block1->getTitle(), $block2->getTitle(), $nonExistentBlockTitle];

        /* When. */
        $blocks = $this->blockService->getByTitles($titles);

        /* Then. */
        $this->assertEquals(2, sizeof($blocks));
        $this->assertEquals($block1->getId(), $blocks[0]->getId());
        $this->assertEquals($block2->getId(), $blocks[1]->getId());
    }

}