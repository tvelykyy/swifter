<?php

namespace Swifter\AdminBundle\Tests\Controller;

class LoginControllerTest extends ControllerTest
{

    public function testShouldRenderLoginPageIfNotAuthenticated()
    {
        /* When. */
        $crawler = $this->client->request('GET', $this->generateRoute('admin_login_page'));

        /* Then. */
        $this->assertEquals(200, $this->getResponse()->getStatusCode());
        $this->assertEquals('Login', $crawler->filter('button[type=submit]')->first()->text());
        $this->assertNotEmpty($crawler->filter('input[type=hidden][name=_csrf_security_token]')->first()->attr('value'));
        $this->assertEquals(1, count($crawler->filter('input[name=email]')));
        $this->assertEquals(1, count($crawler->filter('input[name=password]')));
    }

    public function testShouldRedirectToLandingPageIfRequestingLoginPageBeingAuthenticated()
    {
        /* Given. */
        $this->authenticateAsAdmin();

        /* When. */
        $crawler = $this->client->request('GET', $this->generateRoute('admin_login_page'));

        /* Then. */
        $this->assertEquals(302, $this->getResponse()->getStatusCode());
        $this->assertNotEmpty($this->getResponse()->headers->get('Location'));
    }

}