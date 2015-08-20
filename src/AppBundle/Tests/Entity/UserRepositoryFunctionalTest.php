<?php

namespace AppBundle\Tests\Entity;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class UserRepositoryFunctionalTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

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
    }

    /**
     * @covers \AppBundle\Entity\UserRepository
     */
    //public function testLoadUserByUsername()
    public function testUserRepository()
    {
        $user = $this->em
            ->getRepository('AppBundle:User')
            ->loadUserByUsername('belac');

        $this->assertEquals('belac', $user->getUsername());
//    }
    
 //   public function testRefreshUser()
 //   {
        $user = $this->em
            ->getRepository('AppBundle:User')
            ->loadUserByUsername('belac');

        $this->em->detach($user);
        $user->setUsername('notBelac');
        $serializedUser = serialize($user);
        $unSerializedUser = unserialize($serializedUser);

        $user = $this->em
            ->getRepository('AppBundle:User')
            ->refreshUser($unSerializedUser);

        $this->assertEquals('belac', $user->getUsername());
   // }
    
    //public function testSearchUsers()
    //{
        $criteria = array('email'=>'belackriv@gmail.com');

        $users = $this->em
            ->getRepository('AppBundle:User')
            ->searchUsers($criteria);

        $this->assertCount(1, $users);
        $this->assertEquals('belackriv@gmail.com', $users[0]->getEmail());
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