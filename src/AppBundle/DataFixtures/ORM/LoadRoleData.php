<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Role;

class LoadRoleData extends AbstractFixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $userRole = new Role();
        $userRole->setName('User');
        $userRole->setRole('ROLE_USER');
        $userRole->setIsAllowedToSwitch(false);

        $manager->persist($userRole);

        $leadRole = new Role();
        $leadRole->setName('Lead');
        $leadRole->setRole('ROLE_LEAD');
        $leadRole->setIsAllowedToSwitch(false);
        $leadRole->addRoleToHierarchy($userRole);
        
        $manager->persist($leadRole);

        $adminRole = new Role();
        $adminRole->setName('Admin');
        $adminRole->setRole('ROLE_ADMIN');
        $adminRole->setIsAllowedToSwitch(false);
        $adminRole->addRoleToHierarchy($leadRole);
        
        $manager->persist($adminRole);
     
        $devRole = new Role();
        $devRole->setName('Admin');
        $devRole->setRole('ROLE_DEV');
        $devRole->setIsAllowedToSwitch(true);
        $devRole->addRoleToHierarchy($adminRole);
        
        $manager->persist($devRole);

        

        $manager->flush();

        $this->addReference('ROLE_USER', $userRole);
        $this->addReference('ROLE_LEAD', $leadRole);
        $this->addReference('ROLE_ADMIN', $adminRole);
        $this->addReference('ROLE_DEV', $devRole);
    }

}