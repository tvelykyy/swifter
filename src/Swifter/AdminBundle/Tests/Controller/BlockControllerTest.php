<?php

namespace Swifter\AdminBundle\Tests\Controller;

use Swifter\CommonBundle\DataFixtures\Test\PagesFixtures;
use Swifter\CommonBundle\Entity\Block;
use Swifter\CommonBundle\Entity\Serialization\SerializationGroups;

class BlockControllerTest extends ControllerTest
{
    protected function setUp()
    {
        parent::setUp();
        $this->authenticateAsAdmin();
    }

    public function testShouldReturnAllBlocksInJson()
    {
        /* When. */
        $block1 = $this->fixtures->getReference(PagesFixtures::MAIN_BLOCK);
        $block2 = $this->fixtures->getReference(PagesFixtures::TITLE_BLOCK);
        $block3 = $this->fixtures->getReference(PagesFixtures::FOOTER_BLOCK);
        $blocks = $this->getBlocks();

        /* Then. */
        $this->assertEquals(200, $this->getResponse()->getStatusCode());
        $this->assertEquals('application/json', $this->getResponse()->headers->get('Content-Type'));
        $this->assertEquals(3, sizeof($blocks));
        $this->assertEquals(1, $blocks[0]->id);
        $this->assertEquals($block1->getTitle(), $blocks[0]->title);
        $this->assertEquals(2, $blocks[1]->id);
        $this->assertEquals($block2->getTitle(), $blocks[1]->title);
        $this->assertEquals(3, $blocks[2]->id);
        $this->assertEquals($block3->getTitle(), $blocks[2]->title);
    }

    public function testShouldDeleteBlock()
    {
        /* Given. */
        $blocksBeforeDelete = $this->getBlocks();

        /* When. */
        $this->client->request('DELETE', $this->generateRoute('admin_delete_block', ['id' => 1]));
        $response = $this->getResponse();
        $blocksAfterDelete = $this->getBlocks();

        /* Then. */
        $this->assertEquals(204, $response->getStatusCode());
        $this->assertEquals(sizeof($blocksBeforeDelete), sizeof($blocksAfterDelete) + 1);
    }

    public function testShouldReturn400SavingInvalidBlock()
    {
        /* Given. */
        $block = new Block();
        $block->setTitle('Ti');

        $blocksBeforeSave = $this->getBlocks();

        $serializedBlock = $this->getSerializator()->serializeToJsonByGroup($block, SerializationGroups::LIST_GROUP);
        /* When. */
        $this->doRequest('POST', $this->generateRoute('admin_create_block'), $serializedBlock);
        $response = $this->getResponse();
        $blocksAfterSave = $this->getBlocks();

        /* Then. */
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals($blocksBeforeSave, $blocksAfterSave);
    }

    public function testShouldCreateBlock()
    {
        /* Given. */
        $block = new Block();
        $block->setTitle('Valid Title');

        $blocksBeforeSave = $this->getBlocks();

        $serializedBlock = $this->getSerializator()->serializeToJsonByGroup($block, SerializationGroups::LIST_GROUP);
        /* When. */
        $this->doRequest('POST', $this->generateRoute('admin_create_block'), $serializedBlock);
        $response = $this->getResponse();
        $blocksAfterSave = $this->getBlocks();

        /* Then. */
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertNotEmpty($response->getContent());
        $this->assertEquals(sizeof($blocksBeforeSave) + 1, sizeof($blocksAfterSave));
    }

    public function testShouldEditBlock()
    {
        /* Given. */
        $blocksBeforeSave = $this->getBlocks();
        $block = new Block();
        $block->setId($blocksBeforeSave[0]->id);

        $newTitle = 'NEW_TITLE';
        $block->setTitle($newTitle);

        $serializedBlock = $this->getSerializator()->serializeToJsonByGroup($block, SerializationGroups::LIST_GROUP);

        /* When. */
        $this->doRequest('PUT', $this->generateRoute('admin_edit_block'), $serializedBlock);
        $response = $this->getResponse();
        $blocksAfterSave = $this->getBlocks();

        /* Then. */
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(sizeof($blocksBeforeSave), sizeof($blocksAfterSave));
        $this->assertEquals($newTitle, $blocksAfterSave[0]->title);
    }

    public function testShouldGetBlocksByTitles()
    {
        /* Given. */
        $block1 = $this->fixtures->getReference(PagesFixtures::TITLE_BLOCK);
        $block2 = $this->fixtures->getReference(PagesFixtures::FOOTER_BLOCK);
        $titles = implode(";", [$block1->getTitle(), $block2->getTitle()]);

        /* When. */
        $this->client->request('GET',
            $this->generateRoute('admin_get_blocks_by_titles', ['semicolonSeparatedTitles' => $titles]));
        $response = $this->getResponse();
        $responseJson = json_decode($response->getContent());

        /* Then. */
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(2, sizeof($responseJson));

        $this->assertEquals($block1->getId(), $responseJson[0]->id);
        $this->assertEquals($block1->getTitle(), $responseJson[0]->title);
        $this->assertEquals($block2->getId(), $responseJson[1]->id);
        $this->assertEquals($block2->getTitle(), $responseJson[1]->title);
    }

    private function getBlocks()
    {
        $this->client->request('GET', $this->generateRoute('admin_get_blocks'));
        $blocks = json_decode($this->getResponse()->getContent());

        return $blocks;
    }

    private function doRequest($method, $route, $json)
    {
        $this->client->request(
            $method,
            $route,
            array(),
            array(),
            array(),
            $json
        );
    }

}