<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
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
        $belacUser = $manager->getRepository('AppBundle:User')->findOneBy(['username'=>'belac']);
        $belacUserRole = null;
        if(!$belacUser){
            $belacUser = new User();
            $belacUser->setUsername('belac');
            $belacUser->setEmail('belackriv@gmail.com');
            $belacUser->setFirstName('Belac');
            $belacUser->setLastName('Kriv');
            $belacUser->setIsActive(true);
            $belacUser->setDefaultDepartment($this->getReference('stepDept'));
            $belacUser->setOrganization($this->getReference('stepOrg'));
            $belacUserRole = new UserRole();
            $belacUserRole->setUser($belacUser);
            $belacUserRole->setRole($this->getReference('ROLE_DEV'));
            $belacUser->addUserRole($belacUserRole);


            $belacPassword = 'password';
            $encoder = $this->container->get('security.password_encoder');
            $encoded = $encoder->encodePassword($belacUser, $belacPassword);

            $belacUser->setPassword($encoded);

            $manager->persist($belacUser);
        }

        $plainUser = $manager->getRepository('AppBundle:User')->findOneBy(['username'=>'usertest']);
        $plainUserRole = null;
        if(!$plainUser){
            $plainUser = new User();
            $plainUser->setUsername('usertest');
            $plainUser->setEmail('user@none');
            $plainUser->setFirstName('User');
            $plainUser->setLastName('Test');
            $plainUser->setIsActive(true);
            $plainUser->setDefaultDepartment($this->getReference('oneOne'));
            $plainUser->setOrganization($this->getReference('demoOrg'));
            $plainUserRole = new UserRole();
            $plainUserRole->setUser($plainUser);
            $plainUserRole->setRole($this->getReference('ROLE_USER'));
            $plainUser->addUserRole($plainUserRole);

            $plainPassword = 'password';
            $encoder = $this->container->get('security.password_encoder');
            $encoded = $encoder->encodePassword($plainUser, $plainPassword);

            $plainUser->setPassword($encoded);

            $manager->persist($plainUser);
        }

        $leadUser = $manager->getRepository('AppBundle:User')->findOneBy(['username'=>'leadtest']);
        $leadUserRole = null;
        if(!$leadUser){
            $leadUser = new User();
            $leadUser->setUsername('leadtest');
            $leadUser->setEmail('lead@none');
            $leadUser->setFirstName('Lead');
            $leadUser->setLastName('Test');
            $leadUser->setIsActive(true);
            $leadUser->setDefaultDepartment($this->getReference('oneOne'));
            $leadUser->setOrganization($this->getReference('demoOrg'));
            $leadUserRole = new UserRole();
            $leadUserRole->setUser($leadUser);
            $leadUserRole->setRole($this->getReference('ROLE_LEAD'));
            $leadUser->addUserRole($leadUserRole);

            $leadPassword = 'password';
            $encoder = $this->container->get('security.password_encoder');
            $encoded = $encoder->encodePassword($leadUser, $leadPassword);

            $leadUser->setPassword($encoded);

            $manager->persist($leadUser);
        }

        $adminUser = $manager->getRepository('AppBundle:User')->findOneBy(['username'=>'admintest']);
        $adminUserRole = null;
        if(!$adminUser){
            $adminUser = new User();
            $adminUser->setUsername('admintest');
            $adminUser->setEmail('admin@none');
            $adminUser->setFirstName('Admin');
            $adminUser->setLastName('Test');
            $adminUser->setIsActive(true);
            $adminUser->setDefaultDepartment($this->getReference('oneOne'));
            $adminUser->setOrganization($this->getReference('demoOrg'));
            $adminUserRole = new UserRole();
            $adminUserRole->setUser($adminUser);
            $adminUserRole->setRole($this->getReference('ROLE_ADMIN'));
            $adminUser->addUserRole($adminUserRole);

            $adminPassword = 'password';
            $encoder = $this->container->get('security.password_encoder');
            $encoded = $encoder->encodePassword($adminUser, $adminPassword);

            $adminUser->setPassword($encoded);

            $manager->persist($adminUser);
        }

        $manager->flush();

        $aclProvider = $this->container->get('security.acl.provider');
        $devRoleSecurityIdentity = new RoleSecurityIdentity('ROLE_DEV');
        $adminRoleSecurityIdentity = new RoleSecurityIdentity('ROLE_ADMIN');
        $leadRoleSecurityIdentity = new RoleSecurityIdentity('ROLE_LEAD');
        $userRoleSecurityIdentity = new RoleSecurityIdentity('ROLE_USER');

        $objectIdentity = ObjectIdentity::fromDomainObject($belacUser);
        try {
            $acl = $aclProvider->findAcl($objectIdentity);
        } catch (\Symfony\Component\Security\Acl\Exception\AclNotFoundException $e) {
            $acl = $aclProvider->createAcl($objectIdentity);
            $acl->insertObjectAce($userRoleSecurityIdentity, MaskBuilder::MASK_VIEW);
            $acl->insertObjectAce($devRoleSecurityIdentity, MaskBuilder::MASK_OPERATOR);
            $aclProvider->updateAcl($acl);
        }

        if($belacUserRole){
            $objectIdentity = ObjectIdentity::fromDomainObject($belacUserRole);
            try {
                $acl = $aclProvider->findAcl($objectIdentity);
            } catch (\Symfony\Component\Security\Acl\Exception\AclNotFoundException $e) {
                $acl = $aclProvider->createAcl($objectIdentity);
                $acl->insertObjectAce($userRoleSecurityIdentity, MaskBuilder::MASK_VIEW);
                $acl->insertObjectAce($devRoleSecurityIdentity, MaskBuilder::MASK_OPERATOR);
                $aclProvider->updateAcl($acl);
            }
        }

        $objectIdentity = ObjectIdentity::fromDomainObject($plainUser);
        try {
            $acl = $aclProvider->findAcl($objectIdentity);
        } catch (\Symfony\Component\Security\Acl\Exception\AclNotFoundException $e) {
            $acl = $aclProvider->createAcl($objectIdentity);
            $acl->insertObjectAce($userRoleSecurityIdentity, MaskBuilder::MASK_VIEW);
            $acl->insertObjectAce($adminRoleSecurityIdentity, MaskBuilder::MASK_OPERATOR);
            $aclProvider->updateAcl($acl);
        }

        if($plainUserRole){
            $objectIdentity = ObjectIdentity::fromDomainObject($plainUserRole);
            try {
                $acl = $aclProvider->findAcl($objectIdentity);
            } catch (\Symfony\Component\Security\Acl\Exception\AclNotFoundException $e) {
                $acl = $aclProvider->createAcl($objectIdentity);
                $acl->insertObjectAce($userRoleSecurityIdentity, MaskBuilder::MASK_VIEW);
                $acl->insertObjectAce($adminRoleSecurityIdentity, MaskBuilder::MASK_OPERATOR);
                $aclProvider->updateAcl($acl);
            }
        }

        $objectIdentity = ObjectIdentity::fromDomainObject($leadUser);
        try {
            $acl = $aclProvider->findAcl($objectIdentity);
        } catch (\Symfony\Component\Security\Acl\Exception\AclNotFoundException $e) {
            $acl = $aclProvider->createAcl($objectIdentity);
            $acl->insertObjectAce($userRoleSecurityIdentity, MaskBuilder::MASK_VIEW);
            $acl->insertObjectAce($adminRoleSecurityIdentity, MaskBuilder::MASK_OPERATOR);
            $aclProvider->updateAcl($acl);
        }

        if($leadUserRole){
            $objectIdentity = ObjectIdentity::fromDomainObject($leadUserRole);
            try {
                $acl = $aclProvider->findAcl($objectIdentity);
            } catch (\Symfony\Component\Security\Acl\Exception\AclNotFoundException $e) {
                $acl = $aclProvider->createAcl($objectIdentity);
                $acl->insertObjectAce($userRoleSecurityIdentity, MaskBuilder::MASK_VIEW);
                $acl->insertObjectAce($adminRoleSecurityIdentity, MaskBuilder::MASK_OPERATOR);
                $aclProvider->updateAcl($acl);
            }
        }

        $objectIdentity = ObjectIdentity::fromDomainObject($adminUser);
        try {
            $acl = $aclProvider->findAcl($objectIdentity);
        } catch (\Symfony\Component\Security\Acl\Exception\AclNotFoundException $e) {
            $acl = $aclProvider->createAcl($objectIdentity);
            $acl->insertObjectAce($userRoleSecurityIdentity, MaskBuilder::MASK_VIEW);
            $acl->insertObjectAce($adminRoleSecurityIdentity, MaskBuilder::MASK_OPERATOR);
            $aclProvider->updateAcl($acl);
        }

        if($adminUserRole){
            $objectIdentity = ObjectIdentity::fromDomainObject($adminUserRole);
            try {
                $acl = $aclProvider->findAcl($objectIdentity);
            } catch (\Symfony\Component\Security\Acl\Exception\AclNotFoundException $e) {
                $acl = $aclProvider->createAcl($objectIdentity);
                $acl->insertObjectAce($userRoleSecurityIdentity, MaskBuilder::MASK_VIEW);
                $acl->insertObjectAce($adminRoleSecurityIdentity, MaskBuilder::MASK_OPERATOR);
                $aclProvider->updateAcl($acl);
            }
        }

    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies()
    {
        return ['AppBundle\DataFixtures\ORM\LoadOrganizationData','AppBundle\DataFixtures\ORM\LoadDepartmentData','AppBundle\DataFixtures\ORM\LoadRoleData']; // fixture classes fixture is dependent on
    }
}