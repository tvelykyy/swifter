<?php

namespace Swifter\FrontBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class DispatcherControllerTest extends WebTestCase
{
    protected $client;

    protected function setUp()
    {
        $this->client = static::createClient();

        $classes = array(
            'Swifter\CommonBundle\DataFixtures\Test\LoadPagesData'
        );
        $this->loadFixtures($classes);
    }

    public function testShouldRenderMainPageWithNoQueryParams()
    {
        /* When. */
        $crawler = $this->client->request('GET', '/');

        /* Then. */
        $li = $crawler->filter('li');
        $this->assertEquals(3, $li->count());
        $this->assertEquals('/', $li->eq(0)->text());
        $this->assertEquals('/news', $li->eq(1)->text());
        $this->assertEquals('/news/first', $li->eq(2)->text());
    }

    public function testShouldRenderCyrillicTitle()
    {
        /* When. */
        $crawler = $this->client->request('GET', '/');

        /* Then. */
        $this->assertEquals('Заголовок кирилиця і буква І!', $crawler->filter('title')->text());
    }

    public function testShouldReturn404OnNotFoundPage()
    {
        /* When. */
        $this->client->request('GET', '/not-found');

        /* Then. */
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function testShouldRenderPageWithTemplateInheritance()
    {
        /* When. */
        $crawler = $this->client->request('GET', '/news');

        /* Then. */
        $this->assertContains('This is a news page.', $crawler->filter('section')->text());
        $this->assertContains('Medium footer.', $crawler->filter('footer')->text());
        $this->assertEquals('Заголовок кирилиця і буква І!', $crawler->filter('title')->text());
    }

    public function testShouldRenderPageWithMergingDeficientBlocks()
    {
        /* When. */
        $crawler = $this->client->request('GET', '/news/first');

        /* Then. */
        $this->assertContains('This is a news page.', $crawler->filter('section')->text());
        $this->assertContains('This is super cool footer.', $crawler->filter('footer')->text());
        $this->assertEquals('Заголовок кирилиця і буква І!', $crawler->filter('title')->text());
    }

    public function testShouldRenderPageWithQueryParams()
    {
        /* When. */
        $crawler = $this->client->request('GET', '/', array('offset' => 1, 'limit' => 1));

        /* Then. */
        $li = $crawler->filter('li');
        $this->assertEquals(1, $li->count());
        $this->assertEquals('/news', $li->eq(0)->text());
    }
}
