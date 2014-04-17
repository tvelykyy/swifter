<?php

namespace Swifter\AdminBundle\Tests\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Swifter\AdminBundle\Service\SerializationService;
use Swifter\CommonBundle\Entity\Block;
use Swifter\CommonBundle\Entity\Page;
use Swifter\CommonBundle\Entity\PageBlock;
use Swifter\CommonBundle\Entity\Template;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SerializationServiceTest extends WebTestCase
{
    protected $serializationService;

    const BLOCK_CLASS = 'Swifter\CommonBundle\Entity\Block';
    const BLOCK_ID_1 = 1;
    const BLOCK_TITLE_1 = 'Block Title';

    const PAGE_CLASS = 'Swifter\CommonBundle\Entity\Page';
    const PAGE_ID_1 = 1;
    const PAGE_NAME_1 = 'Main';
    const PAGE_URI_1 = 'main';
    const PAGE_ID_2 = 2;
    const PAGE_NAME_2 = 'News';
    const PAGE_URI_2 = 'news';

    const PAGE_BLOCK_CLASS = 'Swifter\CommonBundle\Entity\Page';
    const PAGE_BLOCK_ID_1 = 1;
    const PAGE_BLOCK_CONTENT_1 = 'Main';

    const TEMPLATE_CLASS = 'Swifter\CommonBundle\Entity\Template';
    const TEMPLATE_ID_1 = 1;
    const TEMPLATE_TITLE_1 = 'Main Template';
    const TEMPLATE_PATH_1 = 'SwifterBundle:FakePath';

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
        $block = $this->initBlock1();

        /* When. */
        $actualJson = $this->serializationService->serializeToJsonByGroup($block, 'list');

        /* Then. */
        $this->assertEquals($expectedJson, $actualJson);
    }

    public function testShouldDeserializeBlockFromJson()
    {
        /* Given. */
        $json = '{"id":'. self::BLOCK_ID_1 .',"title":"'. self::BLOCK_TITLE_1 .'"}';

        /* When. */
        $actualBlock = $this->serializationService->deserializeFromJson($json, self::BLOCK_CLASS);

        /* Then. */
        $this->assertEquals(self::BLOCK_CLASS, get_class($actualBlock));
        $this->assertEquals(self::BLOCK_ID_1, $actualBlock->getId());
        $this->assertEquals(self::BLOCK_TITLE_1, $actualBlock->getTitle());
    }

    public function testShouldSerializePageWithListGroup()
    {
        /* Given. */
        $expectedJson = '{"name":"'. self::PAGE_NAME_1 .'","uri":"'. self::PAGE_URI_1 .'",'.
            '"template":{"title":"'. self::TEMPLATE_TITLE_1. '","path":"'. self::TEMPLATE_PATH_1 .'"}}';

        $page = $this->initFullPage();

        /* When. */
        $actualJson = $this->serializationService->serializeToJsonByGroup($page, 'list');

        /* Then. */
        $this->assertEquals($expectedJson, $actualJson);
    }

    public function testShouldSerializePageWithDetailsGroup()
    {
        /* Given. */
        $expectedJson = '{"name":"'. self::PAGE_NAME_1 .'","uri":"'. self::PAGE_URI_1 .'",'.
            '"parent":{"name":"'. self::PAGE_NAME_2 .'","uri":"'. self::PAGE_URI_2 .'"},'.
            '"page_blocks":[{"block":{"title":"'. self::BLOCK_TITLE_1 .'"},"content":"'. self::PAGE_BLOCK_CONTENT_1 .'"}],'.
            '"template":{"title":"'. self::TEMPLATE_TITLE_1. '","path":"'. self::TEMPLATE_PATH_1 .'"}}';

        $page = $this->initFullPage();

        /* When. */
        $actualJson = $this->serializationService->serializeToJsonByGroup($page, 'details');

        /* Then. */
        $this->assertEquals($expectedJson, $actualJson);
    }

    public function testShouldDeserializePageFromJson()
    {
        /* Given. */
        $json = '{"id":"'. self::PAGE_ID_1 .'","name":"'. self::PAGE_NAME_1 .'","uri":"'. self::PAGE_URI_1 .'",'.
            '"parent":{"name":"'. self::PAGE_NAME_2 .'","uri":"'. self::PAGE_URI_2 .'"},'.
            '"page_blocks":[{"id":"'. self::PAGE_BLOCK_ID_1 .'","block":{"title":"'. self::BLOCK_TITLE_1 .'"},"content":"'. self::PAGE_BLOCK_CONTENT_1 .'"}],'.
            '"template":{"id":"'. self::TEMPLATE_ID_1 .'","title":"'. self::TEMPLATE_TITLE_1. '","path":"'. self::TEMPLATE_PATH_1 .'"}}';

        /* When */
        $page = $this->serializationService->deserializeFromJson($json, self::PAGE_CLASS);

        /* Then. */
        $this->assertEquals(self::PAGE_ID_1, $page->getId());
        $this->assertEquals(self::PAGE_NAME_1, $page->getName());
        $this->assertEquals(self::PAGE_URI_1, $page->getUri());

        $this->assertEquals(self::TEMPLATE_ID_1, $page->getTemplate()->getId());
        $this->assertEquals(self::TEMPLATE_TITLE_1, $page->getTemplate()->getTitle());
        $this->assertEquals(self::TEMPLATE_PATH_1, $page->getTemplate()->getPath());

        $this->assertEquals(self::PAGE_NAME_2, $page->getParent()->getName());
        $this->assertEquals(self::PAGE_URI_2, $page->getParent()->getUri());

        $this->assertEquals(self::PAGE_BLOCK_ID_1, $page->getPageBlocks()->first()->getId());
        $this->assertEquals(self::PAGE_BLOCK_CONTENT_1, $page->getPageBlocks()->first()->getContent());
        $this->assertEquals(self::BLOCK_TITLE_1, $page->getPageBlocks()->first()->getBlock()->getTitle());
    }

    protected function initFullPage()
    {
        $page = $this->initPage1();

        $parentPage = $this->initPage2();
        $page->setParent($parentPage);

        $template = $this->initTemplate1();
        $page->setTemplate($template);


        $block = $this->initBlock1();
        $pageBlock = new PageBlock();
        $pageBlock->setContent(self::PAGE_BLOCK_CONTENT_1);
        $pageBlock->setBlock($block);
        $pageBlock->setPage($page);

        $page->setPageBlocks(new ArrayCollection(array($pageBlock)));

        return $page;
    }

    protected function initPage1()
    {
        $page = new Page();
        $page->setName(self::PAGE_NAME_1);
        $page->setUri(self::PAGE_URI_1);

        return $page;
    }

    protected function initPage2()
    {
        $parentPage = new Page();
        $parentPage->setName(self::PAGE_NAME_2);
        $parentPage->setUri(self::PAGE_URI_2);

        return $parentPage;
    }

    protected function initTemplate1()
    {
        $template = new Template();
        $template->setPath(self::TEMPLATE_PATH_1);
        $template->setTitle(self::TEMPLATE_TITLE_1);

        return $template;
    }

    protected function initBlock1()
    {
        $block = new Block();
        $block->setTitle(self::BLOCK_TITLE_1);

        return $block;
    }



}