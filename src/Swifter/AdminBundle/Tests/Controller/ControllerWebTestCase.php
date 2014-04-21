<?php

namespace Swifter\AdminBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class ControllerWebTestCase extends WebTestCase
{
    public static function createClient()
    {
        return WebTestCase::createClient();
    }
}