<?php

namespace Swifter\AdminBundle\Tests\Controller;

class BlocksControllerTest extends ControllerTest
{
    protected function setUp()
    {
        parent::setUp();
        $this->authenticateAsAdmin();
    }

    public function testShouldReturnAllBlocksInJson()
    {
        /* When. */
        $crawler = $this->client->request('GET', $this->generateRoute('admin_retrieve_blocks'));
        $blocks = json_decode($this->getResponse()->getContent());

        /* Then. */
        $this->assertEquals(200, $this->getResponse()->getStatusCode());
        $this->assertEquals('application/json', $this->getResponse()->headers->get('Content-Type'));
        $this->assertEquals(3, sizeof($blocks));
        $this->assertEquals(1, $blocks[0]->id);
        $this->assertEquals('MAIN_CONTENT', $blocks[0]->title);
        $this->assertEquals(2, $blocks[1]->id);
        $this->assertEquals('TITLE', $blocks[1]->title);
        $this->assertEquals(3, $blocks[2]->id);
        $this->assertEquals('FOOTER', $blocks[2]->title);
    }

    public function testShouldDeleteBlock()
    {
        /* Given. */

        /* When. */

        /* Then. */
    }

}