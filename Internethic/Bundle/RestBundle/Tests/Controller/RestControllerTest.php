<?php

namespace Internethic\Bundle\RestBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RestControllerTest extends WebTestCase
{
    public function testFetchcontenttree()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/fetch_content_tree');
    }

}
