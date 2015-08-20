<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultRestControllerTest extends WebTestCase
{

	/**
     * {@inheritDoc}
     */
    public static function setUpBeforeClass()
    {
        self::bootKernel();
        $container = static::$kernel->getContainer();
        $em = $container
            ->get('doctrine')
            ->getManager();
        
        $loader = new \Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader($container);
        $loader->loadFromDirectory(__DIR__.'/../../DataFixtures/ORM');
        $purger = new \Doctrine\Common\DataFixtures\Purger\ORMPurger($em);
        $executor = new \Doctrine\Common\DataFixtures\Executor\ORMExecutor($em, $purger);
        $executor->execute($loader->getFixtures());
    }


    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        self::bootKernel();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->client = static::createClient(array(), array(
		    'PHP_AUTH_USER' => 'admintest',
		    'PHP_AUTH_PW'   => 'password',
		    'HTTP_ACCEPT' => 'application/json',
		    'CONTENT_TYPE' => 'application/json'
		));

        $this->serializer = $this->client->getContainer()->get('jms_serializer');
    }

    protected function assertJsonResponse($response, $statusCode = 200)
    {
        $this->assertEquals(
            $statusCode, $response->getStatusCode(),
            $response->getContent()
        );
        $this->assertTrue(
            $response->headers->contains('Content-Type', 'application/json'),
            "Content-Type Not JSON: \n".$response->headers
        );
    }

    protected function checkResponseListForKey($response, $keyName)
    {
    	$listObject = $this->serializer->deserialize($response->getContent(), 'array', 'json');
    	$this->assertTrue(!empty($listObject['list']), 'List Should Not Be Empty');
    	foreach($listObject['list'] as $data){
    		$this->assertArrayHasKey($keyName, $data);
    	}
    }

    protected function setEntityIdToNull($entity)
    {
        $reflectedObject = new \ReflectionObject($entity);
        $reflectedProperty = $reflectedObject->getProperty('id');
        $reflectedProperty->setAccessible(true);
        $id = $reflectedProperty->getValue($entity);
        $reflectedProperty->setValue($entity, null);
        return $id;
    }

    public function testCreateTravelerIdAction()
    {
        $this->client->request(
            'POST',
            '/tid',
            array(),
            array(),
            array(),
            '{"travelerId":"AS0TESTCREATE"}'
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response);
        $newEntity = $this->serializer->deserialize($response->getContent(), 'AppBundle\Entity\TravelerId', 'json');
        $this->assertEquals($newEntity->getTravelerId(),"AS0TESTCREATE");
        return $newEntity;
    }

    /**
     * @depends testCreateTravelerIdAction
     */
    public function testListTravelerIdAction($entity)
    {
        $this->client->request('GET', '/tid');
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response);
        $this->checkResponseListForKey($response, 'travelerId');
    }

     /**
     * @depends testCreateTravelerIdAction
     */
    public function testShowTravelerIdAction($entity)
    {
        $this->client->request('GET', '/tid/'.$entity->getId());
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response);
        $shownEntity = $this->serializer->deserialize($response->getContent(), 'AppBundle\Entity\TravelerId', 'json');
        $this->assertEquals($shownEntity->getId(), $entity->getId());
    }

    /**
     * @depends testCreateTravelerIdAction
     */
    public function testUpdateTravelerIdAction($entity)
    {
        $entity->setTravelerId('AS0TESTUPDATE');
        $json = $this->serializer->serialize($entity,'json');
        $this->client->request(
            'PUT',
            '/tid/'.$entity->getId(),
            array(),
            array(),
            array(),
            $json
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response);
        $updatedEntity = $this->serializer->deserialize($response->getContent(), 'AppBundle\Entity\TravelerId', 'json');
        $this->assertEquals($updatedEntity->getTravelerId(),'AS0TESTUPDATE');
    }

    /**
     * @depends testCreateTravelerIdAction
     */
    public function testPatchTravelerIdAction($entity)
    {
        $entityId = $this->setEntityIdToNull($entity);
        $entity->setTravelerId('AS0TESTPATCH');
        $json = $this->serializer->serialize($entity,'json');
        $this->client->request(
            'PATCH',
            '/tid/'.$entityId,
            array(),
            array(),
            array(),
            $json
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response);
        $updatedEntity = $this->serializer->deserialize($response->getContent(), 'AppBundle\Entity\TravelerId', 'json');
        $this->assertEquals($updatedEntity->getTravelerId(),'AS0TESTPATCH');
    }

    public function testListOfficeAction()
    {
        $this->client->request('GET', '/office');
		$response = $this->client->getResponse();
		$this->assertJsonResponse($response);
		$this->checkResponseListForKey($response, 'name');
    }

    public function testListDepartmentAction()
    {
        $this->client->request('GET', '/department');
		$response = $this->client->getResponse();
		$this->assertJsonResponse($response);
		$this->checkResponseListForKey($response, 'office');
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        $this->em->close();
    }

}
