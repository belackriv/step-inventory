<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\User;
use AppBundle\Entity\UserRole;

class LoadUserData extends AbstractFixture implements DependentFixtureInterface, ContainerAwareInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }


    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $belacUser = new User();
        $belacUser->setUsername('belac');
        $belacUser->setEmail('belackriv@gmail.com');
        $belacUser->setFirstName('Belac');
        $belacUser->setLastName('Kriv');
        $belacUser->setIsActive(true);
        $belacUser->setDefaultDepartment($this->getReference('dfwCheckin'));
        $belacUserRole = new UserRole();
        $belacUserRole->setUser($belacUser);
        $belacUserRole->setRole($this->getReference('ROLE_DEV'));
        $belacUser->addRole($belacUserRole);

        //$belacPassword = 'sup3rg0su';
        $belacPassword = 'password';
        $encoder = $this->container->get('security.password_encoder');
        $encoded = $encoder->encodePassword($belacUser, $belacPassword);

        $belacUser->setPassword($encoded);

        $manager->persist($belacUser);

        $plainUser = new User();
        $plainUser->setUsername('usertest');
        $plainUser->setEmail('user@none');
        $plainUser->setFirstName('User');
        $plainUser->setLastName('Test');
        $plainUser->setIsActive(true);
        $plainUser->setDefaultDepartment($this->getReference('dfwCheckin'));
        $plainUserRole = new UserRole();
        $plainUserRole->setUser($plainUser);
        $plainUserRole->setRole($this->getReference('ROLE_USER'));
        $plainUser->addRole($plainUserRole);

        $plainPassword = 'password';
        $encoder = $this->container->get('security.password_encoder');
        $encoded = $encoder->encodePassword($plainUser, $plainPassword);

        $plainUser->setPassword($encoded);

        $manager->persist($plainUser);

        $leadUser = new User();
        $leadUser->setUsername('leadtest');
        $leadUser->setEmail('lead@none');
        $leadUser->setFirstName('Lead');
        $leadUser->setLastName('Test');
        $leadUser->setIsActive(true);
        $leadUser->setDefaultDepartment($this->getReference('dfwCheckin'));
        $leadUserRole = new UserRole();
        $leadUserRole->setUser($leadUser);
        $leadUserRole->setRole($this->getReference('ROLE_LEAD'));
        $leadUser->addRole($leadUserRole);

        //$leadPassword = 'tsetdael';
        $leadPassword = 'password';
        $encoder = $this->container->get('security.password_encoder');
        $encoded = $encoder->encodePassword($leadUser, $leadPassword);

        $leadUser->setPassword($encoded);

        $manager->persist($leadUser);

        $adminUser = new User();
        $adminUser->setUsername('admintest');
        $adminUser->setEmail('admin@none');
        $adminUser->setFirstName('Admin');
        $adminUser->setLastName('Test');
        $adminUser->setIsActive(true);
        $adminUser->setDefaultDepartment($this->getReference('dfwCheckin'));
        $adminUserRole = new UserRole();
        $adminUserRole->setUser($adminUser);
        $adminUserRole->setRole($this->getReference('ROLE_ADMIN'));
        $adminUser->addRole($adminUserRole);

        //$adminPassword = 'tsetnimda';
        $adminPassword = 'password';
        $encoder = $this->container->get('security.password_encoder');
        $encoded = $encoder->encodePassword($adminUser, $adminPassword);

        $adminUser->setPassword($encoded);

        $manager->persist($adminUser);

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies()
    {
        return array('AppBundle\DataFixtures\ORM\LoadRoleData'); // fixture classes fixture is dependent on
    }
}