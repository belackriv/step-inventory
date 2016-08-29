<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLogin()
    {
        $client = static::createClient();
		$client->request('GET', '/login');

		$this->assertEquals(200, $client->getResponse()->getStatusCode());

    }

    public function testLogout()
    {

		$client = static::createClient();

        $crawler = $client->request('GET', '/logout');
		$this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testLoginCheck()
    {

		$client = static::createClient(array(), array(
		    'PHP_AUTH_USER' => 'admintest',
		    'PHP_AUTH_PW'   => 'password',
		));

        $crawler = $client->request('GET', '/login_check');
        
		//$this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
