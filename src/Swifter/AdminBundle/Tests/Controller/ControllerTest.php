<?php

namespace Swifter\AdminBundle\Tests\Controller;

abstract class ControllerTest extends \PHPUnit_Extensions_Database_TestCase
{
    protected $client;

    protected function setUp()
    {
        parent::setUp();
        $this->client = ControllerWebTestCase::createClient();
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

    protected function getConnection()
    {
        return ControllerWebTestCase::getDbConnection();
    }

    protected function getDataSet()
    {

    }

}