<?php

namespace Tests\AppBundle\Entity;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RoleRepositoryFunctionalTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected static $em;

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
        self::$em = $em;
    }

    public function testFetchRoleHierarchy()
    {
        $roleHierarchy = self::$em
            ->getRepository('AppBundle:Role')
            ->fetchRoleHierarchy();

        //fixtures load 4 element hierachy
        $this->assertCount(4, $roleHierarchy);
    }

    /**
     * {@inheritDoc}
     */
    public static function tearDownAfterClass()
    {
        self::$em->close();
        parent::tearDownAfterClass();
    }
}