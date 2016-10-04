<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FavoriteControllerTest extends WebTestCase
{
    public function testFavoritelist()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/favoriteList');
    }

}
