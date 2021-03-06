<?php

namespace Swifter\AdminBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;

abstract class ControllerTest extends WebTestCase
{
    protected $client;
    protected $fixtures;

    protected function setUp()
    {
        parent::setUp();
        $this->client = static::createClient();

        $classes = array(
            'Swifter\CommonBundle\DataFixtures\Test\PagesFixtures',
            'Swifter\CommonBundle\DataFixtures\Test\UsersFixtures'
        );
        $this->fixtures = $this->loadFixtures($classes)->getReferenceRepository();
    }

    protected function generateRoute($routeName, $parameters = array())
    {
        return $this->client->getContainer()->get('router')->generate($routeName, $parameters);
    }

    protected function authenticateAsAdmin()
    {
        $crawler = $this->client->request('GET', $this->generateRoute('admin_ui_login'));
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

    protected function getSerializator()
    {
        return $this->getContainer()->get('admin.service.serialization');
    }

}