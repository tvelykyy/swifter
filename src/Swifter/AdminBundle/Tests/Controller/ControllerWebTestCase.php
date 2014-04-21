<?php

namespace Swifter\AdminBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class ControllerWebTestCase extends WebTestCase
{
    public static function createClient()
    {
        return WebTestCase::createClient();
    }

    public static function getDbConnection()
    {
        if (null === static::$kernel) {
            static::$kernel = static::createKernel();
            static::$kernel->boot();
        }
        static::$kernel->getContainer()->get('database_connection');
    }
}