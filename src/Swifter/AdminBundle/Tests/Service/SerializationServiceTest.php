<?php

namespace Swifter\AdminBundle\Tests\Service;

use Swifter\AdminBundle\Service\SerializationService;
use Swifter\CommonBundle\Entity\Block;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SerializationServiceTest extends WebTestCase
{
    protected $serializationService;

    const BLOCK_CLASS = 'Swifter\CommonBundle\Entity\Block';
    const BLOCK_TITLE_1 = 'block_title_1';
    const BLOCK_ID_1 = 1;

    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();

        $this->serializationService = new SerializationService(self::$kernel->getContainer());
    }

    public function testShouldSerializeBlockWithListGroup()
    {
        /* Given. */
        $expectedJson = '{"title":"'. self::BLOCK_TITLE_1 .'"}';
        $block = new Block();
        $block->setTitle(self::BLOCK_TITLE_1);

        /* When. */
        $actualJson = $this->serializationService->serializeToJsonByGroup($block, 'list');

        /* Then. */
        $this->assertEquals($expectedJson, $actualJson);
    }

    public function testShouldDeserializeBlockFromJson()
    {
        /* Given. */
        $json = '{"id":'. self::BLOCK_ID_1 .',"title":"'. self::BLOCK_TITLE_1 .'"}';
        $block = new Block();
        $block->setTitle(self::BLOCK_TITLE_1);

        /* When. */
        $actualBlock = $this->serializationService->deserializeFromJson($json, self::BLOCK_CLASS);

        /* Then. */
        $this->assertEquals(self::BLOCK_CLASS, get_class($actualBlock));
        $this->assertEquals(self::BLOCK_ID_1, $actualBlock->getId());
        $this->assertEquals(self::BLOCK_TITLE_1, $actualBlock->getTitle());
    }
}