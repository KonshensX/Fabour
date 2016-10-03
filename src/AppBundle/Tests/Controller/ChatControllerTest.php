<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ChatControllerTest extends WebTestCase
{
    public function testChat()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/chat');
    }

}
