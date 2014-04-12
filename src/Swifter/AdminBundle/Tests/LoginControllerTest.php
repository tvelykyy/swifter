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
        $this->assertEquals('Login', $crawler->filter('button[type=submit]')->first()->text());
        $this->assertNotEmpty($crawler->filter('input[type=hidden][name=_csrf_security_token]')->first()->attr('value'));
    }

    public function testShouldRedirectToLandingPageIfRequestingLoginPageBeingAuthenticated()
    {
        /* Given. */
        $crawler = $this->client->request('GET', 'admin/login');
        $loginButton = $crawler->selectButton('Login');
        $form = $loginButton->form(array(
            'email'     => 'admin@m.com',
            'password'  => 'admin',
        ));
        $this->client->submit($form);

        /* When. */
        $crawler = $this->client->request('GET', 'admin/login');

        /* Then. */
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }
}