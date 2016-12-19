<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class Admin\CityControllerControllerTest extends WebTestCase
{
    public function testAddcity()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/addCity');
    }

}
