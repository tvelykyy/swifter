<?php

namespace Swifter\FrontBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DispatcherControllerTest extends WebTestCase
{
    protected $client;

    protected function setUp()
    {
        $this->client = static::createClient();
    }
    public function testShouldRenderMainPageWithNoQueryParams()
    {
        /* Given. */

        /* When. */
        $crawler = $this->client->request('GET', '/');

        /* Then. */
        $li = $crawler->filter('li');
        $this->assertEquals(3, $li->count());
        $this->assertEquals('/', $li->eq(0)->text());
        $this->assertEquals('/news', $li->eq(1)->text());
        $this->assertEquals('/news/first', $li->eq(2)->text());
//        $this->assertTrue($crawler->filter('html:contains("Hello Fabien")')->count() > 0);
    }

    public function testShouldRenderCyrillicTitle()
    {
        /* Given. */

        /* When. */
        $crawler = $this->client->request('GET', '/');

        /* Then. */
        $this->assertEquals('Заголовок кирилиця і буква І!', $crawler->filter('title')->text());
    }

    public function testShouldReturn404OnNotFoundPage()
    {
        /* Given. */

        /* When. */
        $this->client->request('GET', '/not-found');

        /* Then. */
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function testShouldRenderPageWithTemplateInheritance()
    {
        /* Given. */

        /* When. */
        $crawler = $this->client->request('GET', '/news');

        /* Then. */
        $this->assertContains('This is a news page.', $crawler->filter('section')->text());
        $this->assertContains('Medium footer.', $crawler->filter('footer')->text());
    }
}
