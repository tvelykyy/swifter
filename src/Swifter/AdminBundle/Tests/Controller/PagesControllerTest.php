<?php

namespace Swifter\AdminBundle\Tests\Controller;

class PagesControllerTest extends ControllerTest
{
    protected function setUp()
    {
        parent::setUp();
        $this->authenticateAsAdmin();
    }

    public function testShouldReturnAllPagesInJson()
    {
        /* When. */
        $pages = $this->getPages();

        /* Then. */
        $this->assertEquals(200, $this->getResponse()->getStatusCode());
        $this->assertEquals('application/json', $this->getResponse()->headers->get('Content-Type'));
        $this->assertEquals(3, sizeof($pages));
        $this->assertEquals('Main', $pages[0]->name);
        $this->assertEquals('/', $pages[0]->uri);
        $this->assertEquals('Main Template', $pages[0]->template->title);

        $this->assertEquals('News', $pages[1]->name);
        $this->assertEquals('/news', $pages[1]->uri);
        $this->assertEquals('Main Template', $pages[1]->template->title);

        $this->assertEquals('First News', $pages[2]->name);
        $this->assertEquals('/news/first', $pages[2]->uri);
        $this->assertEquals('Main Template', $pages[2]->template->title);
    }

    protected function getPages()
    {
        $this->client->request('GET', $this->generateRoute('admin_get_pages'));
        $blocks = json_decode($this->getResponse()->getContent());

        return $blocks;
    }

}