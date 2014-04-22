<?php

namespace Swifter\AdminBundle\Tests\Controller;

use Swifter\CommonBundle\Entity\Block;

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
        $blocks = $this->retrieveBlocks();

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
        $blocksBeforeDelete = $this->retrieveBlocks();

        /* When. */
        $this->client->request('DELETE', $this->generateRoute('admin_delete_block', array('id' => 1)));
        $response = $this->getResponse();
        $blocksAfterDelete = $this->retrieveBlocks();

        /* Then. */
        $this->assertEquals(204, $response->getStatusCode());
        $this->assertEquals(sizeof($blocksBeforeDelete), sizeof($blocksAfterDelete) + 1);
    }

    public function testShouldReturn400SavingInvalidBlock()
    {
        /* Given. */
        $block = new Block();
        $block->setTitle('Ti');

        $blocksBeforeSave = $this->retrieveBlocks();

        $serializedBlock = $this->getSerializator()->serializeToJsonByGroup($block, 'list');
        /* When. */
        $this->doSaveRequest($serializedBlock);
        $response = $this->getResponse();
        $blocksAfterSave = $this->retrieveBlocks();

        /* Then. */
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals($blocksBeforeSave, $blocksAfterSave);
    }

    public function testShouldCreateBlock()
    {
        /* Given. */
        $block = new Block();
        $block->setTitle('Valid Title');

        $blocksBeforeSave = $this->retrieveBlocks();

        $serializedBlock = $this->getSerializator()->serializeToJsonByGroup($block, 'list');
        /* When. */
        $this->doSaveRequest($serializedBlock);
        $response = $this->getResponse();
        $blocksAfterSave = $this->retrieveBlocks();

        /* Then. */
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertGreaterThan(0, $response->getContent());
        $this->assertEquals(sizeof($blocksBeforeSave) + 1, sizeof($blocksAfterSave));
    }

    protected function retrieveBlocks()
    {
        $this->client->request('GET', $this->generateRoute('admin_retrieve_blocks'));
        $blocks = json_decode($this->getResponse()->getContent());

        return $blocks;
    }

    protected function getSerializator()
    {
        return $this->getContainer()->get('admin.service.serialization');
    }

    protected function doSaveRequest($json)
    {
        $this->client->request(
            'POST',
            $this->generateRoute('admin_save_block'),
            array(),
            array(),
            array(),
            $json
        );
    }

}