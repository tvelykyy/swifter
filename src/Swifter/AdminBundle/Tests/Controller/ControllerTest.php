<?php

namespace Swifter\AdminBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

abstract class ControllerTest extends WebTestCase
{
    protected $client;

    protected function setUp()
    {
        parent::setUp();
        $this->client = static::createClient();

        $classes = array(
            'Swifter\CommonBundle\DataFixtures\Test\LoadPagesData',
            'Swifter\CommonBundle\DataFixtures\Test\LoadUsersData'
        );
        $this->loadFixtures($classes);
    }

    protected function generateRoute($routeName, $parameters = array())
    {
        return $this->client->getContainer()->get('router')->generate($routeName, $parameters);
    }

    protected function authenticateAsAdmin()
    {
        $crawler = $this->client->request('GET', $this->generateRoute('admin_login_page'));
        $loginButton = $crawler->selectButton('Login');
        $form = $loginButton->form(array(
            'email' => 'admin@m.com',
            'password' => 'admin',
        ));

        $this->client->submit($form);
    }

    protected function getResponse()
    {
        return $this->client->getResponse();
    }

}