<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient(array(), array(
		    'PHP_AUTH_USER' => 'admintest',
		    'PHP_AUTH_PW'   => 'password',
		));

        $crawler = $client->request('GET', '/');
		$this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

     public function testAdmin()
    {
        $client = static::createClient(array(), array(
		    'PHP_AUTH_USER' => 'usertest',
		    'PHP_AUTH_PW'   => 'password',
		));

        $crawler = $client->request('GET', '/admin');
		$this->assertEquals(403, $client->getResponse()->getStatusCode());

		$client = static::createClient(array(), array(
		    'PHP_AUTH_USER' => 'admintest',
		    'PHP_AUTH_PW'   => 'password',
		));

        $crawler = $client->request('GET', '/admin');
		$this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
