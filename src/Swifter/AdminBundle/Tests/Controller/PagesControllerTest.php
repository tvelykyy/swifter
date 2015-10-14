<?php

namespace Swifter\AdminBundle\Tests\Controller;

use Swifter\CommonBundle\Entity\Page;

class PageControllerTest extends ControllerTest
{
    protected function setUp()
    {
        parent::setUp();
        $this->authenticateAsAdmin();
    }

    public function testShouldReturnAllPagesInJson()
    {
        /* Given. */
        $page1 = $this->fixtures->getReference('main-page');
        $page2 = $this->fixtures->getReference('news-page');
        $page3 = $this->fixtures->getReference('news-first-page');

        /* When. */
        $pages = $this->getPages();

        /* Then. */
        $this->assertEquals(200, $this->getResponse()->getStatusCode());
        $this->assertEquals('application/json', $this->getResponse()->headers->get('Content-Type'));
        $this->assertEquals(3, sizeof($pages));
        $this->assertEquals($page1->getName(), $pages[0]->name);
        $this->assertEquals($page1->getUri(), $pages[0]->uri);
        $this->assertEquals($page1->getTemplate()->getTitle(), $pages[0]->template->title);

        $this->assertEquals($page2->getName(), $pages[1]->name);
        $this->assertEquals($page2->getUri(), $pages[1]->uri);
        $this->assertEquals($page2->getTemplate()->getTitle(), $pages[1]->template->title);

        $this->assertEquals($page3->getName(), $pages[2]->name);
        $this->assertEquals($page3->getUri(), $pages[2]->uri);
        $this->assertEquals($page3->getTemplate()->getTitle(), $pages[2]->template->title);
    }

    private function getPages()
    {
        $this->client->request('GET', $this->generateRoute('admin_get_pages'));
        $pages = json_decode($this->getResponse()->getContent());

        return $pages;
    }

    public function testShouldReturnPagesByNameLike()
    {
        /* Given. */
        $newsPage = $this->fixtures->getReference('news-page');

        /* When. */
        $this->client->request('GET', $this->generateRoute('admin_get_pages_by_name_like', ['name' => $newsPage->getName()]));
        $response = $this->getResponse();
        $responseJson = json_decode($response->getContent());

        /* Then. */
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(2, sizeof($responseJson));

        $this->assertContains($newsPage->getName(), $responseJson[0]->name);
        $this->assertContains($newsPage->getName(), $responseJson[1]->name);
    }

    public function testShouldReturnPageWithMergedBlocks()
    {
        /* Given. */
        $newsFistPage = $this->fixtures->getReference('news-first-page');
        $mainContentBlock = $this->fixtures->getReference('news-page-main-content-block')->getContent();
        $titleBlock = $this->fixtures->getReference('main-page-title-block')->getContent();
        $footerBlock = $this->fixtures->getReference('first-news-page-footer-block')->getContent();
        $blocks = [$mainContentBlock, $titleBlock, $footerBlock];

        /* When. */
        $this->client->request('GET', $this->generateRoute('admin_get_page_blocks', ['id' => $newsFistPage->getId()]));
        $response = $this->getResponse();
        $responseJson = json_decode($response->getContent());

        /* Then. */
        $this->assertEquals(200, $response->getStatusCode());
        foreach ($responseJson->pageBlocks as $pageBlock)
        {
            $this->assertContains($pageBlock->content, $blocks);
        }
    }

    public function testShouldCreatePage()
    {
        /* Given. */
        $newsFirstPage = $this->fixtures->getReference('news-first-page');
        $template = $this->fixtures->getReference('main-template');

        $page = new Page();
        $page->setName('new-page');
        $page->setUri($newsFirstPage->getUri().'/new-page');
        $page->setParent($newsFirstPage);
        $page->setTemplate($template);

        /* When. */
        $response = $this->savePageAndGetResponse($page);

        /* Then. */
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertNotEmpty($response->getContent());
    }

    public function testShouldEditPage()
    {
        /* Given. */
        $page = $this->fixtures->getReference('news-first-page');
        $page->setName('updated-name');

        /* When. */
        $response = $this->savePageAndGetResponse($page);

        /* Then. */
        $this->assertEquals(204, $response->getStatusCode());
        $this->assertEmpty($response->getContent());
    }

    public function testShouldNotPassPageValidation()
    {
        /* Given. */
        $page = $this->fixtures->getReference('news-first-page');
        $page->setUri(null);

        /* When. */
        $response = $this->savePageAndGetResponse($page);

        /* Then. */
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('uri', $response->getContent());
    }

    private function savePageAndGetResponse($page)
    {
        $pageJson = $this->getSerializator()->serializeToJsonByGroup($page, 'details');
        $this->client->request('POST',
            $this->generateRoute('admin_save_page'),
            array(),
            array(),
            array(),
            $pageJson
        );
        $response = $this->getResponse();

        return $response;
    }

}