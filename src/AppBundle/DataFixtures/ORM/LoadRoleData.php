<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use AppBundle\Entity\Role;

class LoadRoleData extends AbstractFixture implements ContainerAwareInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $userRole = $manager->getRepository('AppBundle:Role')->findOneBy(['role'=>'ROLE_USER']);
        if(!$userRole){
            $userRole = new Role();
            $userRole->setName('User');
            $userRole->setRole('ROLE_USER');
            $userRole->setIsAllowedToSwitch(false);
            $manager->persist($userRole);
        }

        $leadRole = $manager->getRepository('AppBundle:Role')->findOneBy(['role'=>'ROLE_LEAD']);
        if(!$leadRole){
            $leadRole = new Role();
            $leadRole->setName('Lead');
            $leadRole->setRole('ROLE_LEAD');
            $leadRole->setIsAllowedToSwitch(false);
            $leadRole->addRoleToHierarchy($userRole);
            $manager->persist($leadRole);
        }

        $adminRole = $manager->getRepository('AppBundle:Role')->findOneBy(['role'=>'ROLE_ADMIN']);
        if(!$adminRole){
            $adminRole = new Role();
            $adminRole->setName('Admin');
            $adminRole->setRole('ROLE_ADMIN');
            $adminRole->setIsAllowedToSwitch(false);
            $adminRole->addRoleToHierarchy($leadRole);
            $manager->persist($adminRole);
        }

        $devRole = $manager->getRepository('AppBundle:Role')->findOneBy(['role'=>'ROLE_DEV']);
        if(!$devRole){
            $devRole = new Role();
            $devRole->setName('Dev');
            $devRole->setRole('ROLE_DEV');
            $devRole->setIsAllowedToSwitch(true);
            $devRole->addRoleToHierarchy($adminRole);
            $manager->persist($devRole);
        }



        $manager->flush();

        $this->addReference('ROLE_USER', $userRole);
        $this->addReference('ROLE_LEAD', $leadRole);
        $this->addReference('ROLE_ADMIN', $adminRole);
        $this->addReference('ROLE_DEV', $devRole);

        $aclProvider = $this->container->get('security.acl.provider');
        $devRoleSecurityIdentity = new RoleSecurityIdentity('ROLE_DEV');
        $adminRoleSecurityIdentity = new RoleSecurityIdentity('ROLE_ADMIN');
        $leadRoleSecurityIdentity = new RoleSecurityIdentity('ROLE_LEAD');
        $userRoleSecurityIdentity = new RoleSecurityIdentity('ROLE_USER');

        $objectIdentity = ObjectIdentity::fromDomainObject($userRole);
        try {
            $acl = $aclProvider->findAcl($objectIdentity);
        } catch (\Symfony\Component\Security\Acl\Exception\AclNotFoundException $e) {
            $acl = $aclProvider->createAcl($objectIdentity);
            $acl->insertObjectAce($userRoleSecurityIdentity, MaskBuilder::MASK_VIEW);
            $acl->insertObjectAce($devRoleSecurityIdentity, MaskBuilder::MASK_OPERATOR);
            $aclProvider->updateAcl($acl);
        }

        $objectIdentity = ObjectIdentity::fromDomainObject($leadRole);
        try {
            $acl = $aclProvider->findAcl($objectIdentity);
        } catch (\Symfony\Component\Security\Acl\Exception\AclNotFoundException $e) {
            $acl = $aclProvider->createAcl($objectIdentity);
            $acl->insertObjectAce($userRoleSecurityIdentity, MaskBuilder::MASK_VIEW);
            $acl->insertObjectAce($devRoleSecurityIdentity, MaskBuilder::MASK_OPERATOR);
            $aclProvider->updateAcl($acl);
        }

        $objectIdentity = ObjectIdentity::fromDomainObject($adminRole);
        try {
            $acl = $aclProvider->findAcl($objectIdentity);
        } catch (\Symfony\Component\Security\Acl\Exception\AclNotFoundException $e) {
            $acl = $aclProvider->createAcl($objectIdentity);
            $acl->insertObjectAce($userRoleSecurityIdentity, MaskBuilder::MASK_VIEW);
            $acl->insertObjectAce($devRoleSecurityIdentity, MaskBuilder::MASK_OPERATOR);
            $aclProvider->updateAcl($acl);
        }

        $objectIdentity = ObjectIdentity::fromDomainObject($devRole);
        try {
            $acl = $aclProvider->findAcl($objectIdentity);
        } catch (\Symfony\Component\Security\Acl\Exception\AclNotFoundException $e) {
            $acl = $aclProvider->createAcl($objectIdentity);
            $acl->insertObjectAce($devRoleSecurityIdentity, MaskBuilder::MASK_OPERATOR);
            $aclProvider->updateAcl($acl);
        }
    }

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

}