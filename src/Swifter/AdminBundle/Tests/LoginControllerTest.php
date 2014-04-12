<?php

namespace Swifter\AdminBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginControllerTest extends WebTestCase
{
    protected $client;

    protected function setUp()
    {
        $this->client = static::createClient();
    }

    public function testShouldRenderLoginIfNotAuthenticated()
    {
        /* When. */
        $crawler = $this->client->request('GET', 'admin/login');

        /* Then. */
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }
}