<?php

namespace Swifter\AdminBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class ControllerTest extends WebTestCase
{
    protected $client;

    protected function setUp()
    {
        $this->client = static::createClient();
    }

    protected function generateRoute($routeName)
    {
        return $this->client->getContainer()->get('router')->generate($routeName);
    }

}