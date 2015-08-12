<?php

namespace Swifter\CommonBundle\Tests\Service;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Swifter\CommonBundle\Service\PageService;

class PageServiceTest extends WebTestCase
{
    private $pageService;
    private $fixtures;

    public function __construct()
    {
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $this->pageService = new PageService($em);
    }

    protected function setUp()
    {
        parent::setUp();

        $classes = [
            'Swifter\CommonBundle\DataFixtures\Test\LoadPagesData'
        ];
        $this->fixtures = $this->loadFixtures($classes)->getReferenceRepository();
    }

    public function testGetOneById()
    {
        /* Given. */
        $expected = $this->fixtures->getReference('main-page');

        /* When. */
        $actual = $this->pageService->getOneById($expected->getId());

        /* Then. */
        $this->assertEquals($expected->getId(), $actual->getId());
        $this->assertEquals($expected->getUri(), $actual->getUri());
        $this->assertEquals($expected->getName(), $actual->getName());
    }

    public function testGetOneByUri()
    {
        /* Given. */
        $expected = $this->fixtures->getReference('news-page');

        /* When. */
        $actual = $this->pageService->getOneByUri($expected->getUri());

        /* Then. */
        $this->assertEquals($expected->getId(), $actual->getId());
        $this->assertEquals($expected->getUri(), $actual->getUri());
        $this->assertEquals($expected->getName(), $actual->getName());
    }

    public function testGetAll()
    {
        /* Given. */
        $page1 = $this->fixtures->getReference('main-page');
        $page2 = $this->fixtures->getReference('news-page');
        $page3 = $this->fixtures->getReference('news-first-page');

        /* When. */
        $pages = $this->pageService->getAll();

        /* Then. */
        $this->assertEquals(3, sizeof($pages));
        $this->assertEquals($page1->getId(), $pages[0]->getId());
        $this->assertEquals($page2->getId(), $pages[1]->getId());
        $this->assertEquals($page3->getId(), $pages[2]->getId());
    }

    public function testGetByNameLike()
    {
        /* Given. */
        $newsPage = $this->fixtures->getReference('news-page');
        $newsFirstPage = $this->fixtures->getReference('news-first-page');

        /* When. */
        $pages = $this->pageService->getByNameLike($newsPage->getName());

        /* Then. */
        $this->assertEquals(2, sizeof($pages));
        $this->assertEquals($newsPage->getId(), $pages[0]->getId());
        $this->assertEquals($newsFirstPage->getId(), $pages[1]->getId());
    }
}