<?php

namespace Swifter\AdminBundle\Tests\Controller;

use Swifter\CommonBundle\DataFixtures\Test\PagesFixtures;
use Swifter\CommonBundle\Entity\Page;
use Swifter\CommonBundle\Entity\Serialization\SerializationGroups;

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
        $page1 = $this->fixtures->getReference(PagesFixtures::PARENT_PAGE);
        $page2 = $this->fixtures->getReference(PagesFixtures::CHILD_PAGE);
        $page3 = $this->fixtures->getReference(PagesFixtures::GRAND_CHILD_PAGE);

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
        $newsPage = $this->fixtures->getReference(PagesFixtures::CHILD_PAGE);

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
        $newsFistPage = $this->fixtures->getReference(PagesFixtures::GRAND_CHILD_PAGE);
        $mainContentBlock = $this->fixtures->getReference(PagesFixtures::CHILD_PAGE_MAIN_BLOCK)->getContent();
        $titleBlock = $this->fixtures->getReference(PagesFixtures::PARENT_PAGE_TITLE_BLOCK)->getContent();
        $footerBlock = $this->fixtures->getReference(PagesFixtures::GRAND_CHILD_PAGE_FOOTER_BLOCK)->getContent();
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
        $newsFirstPage = $this->fixtures->getReference(PagesFixtures::GRAND_CHILD_PAGE);
        $template = $this->fixtures->getReference(PagesFixtures::MAIN_TEMPLATE);

        $page = new Page();
        $page->setName('new-page');
        $page->setUri($newsFirstPage->getUri().'/new-page');
        $page->setParent($newsFirstPage);
        $page->setTemplate($template);

        /* When. */
        $response = $this->savePageAndGetResponse('POST', $this->generateRoute('admin_create_page'), $page);

        /* Then. */
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertNotEmpty($response->getContent());
    }

    public function testShouldEditPage()
    {
        /* Given. */
        $page = $this->fixtures->getReference(PagesFixtures::GRAND_CHILD_PAGE);
        $page->setName('updated-name');

        /* When. */
        $response = $this->savePageAndGetResponse('PUT', $this->generateRoute('admin_edit_page'), $page);

        /* Then. */
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotEmpty($response->getContent());
    }

    public function testShouldNotPassPageValidation()
    {
        /* Given. */
        $page = $this->fixtures->getReference(PagesFixtures::GRAND_CHILD_PAGE);
        $page->setUri(null);

        /* When. */
        $response = $this->savePageAndGetResponse('POST', $this->generateRoute('admin_create_page'), $page);

        /* Then. */
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertContains('uri', $response->getContent());
    }

    private function savePageAndGetResponse($method, $route, $page)
    {
        $pageJson = $this->getSerializator()->serializeToJsonByGroup($page, SerializationGroups::DETAILS_GROUP);
        $this->client->request($method,
            $route,
            array(),
            array(),
            array(),
            $pageJson
        );
        $response = $this->getResponse();

        return $response;
    }

}