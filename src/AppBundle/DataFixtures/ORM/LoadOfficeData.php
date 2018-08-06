<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use AppBundle\Entity\Office;

class LoadOfficeData extends AbstractFixture implements DependentFixtureInterface, ContainerAwareInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $stepOffice = $manager->getRepository('AppBundle:Office')->findOneBy([
            'name'=>'Home',
            'organization' => $this->getReference('stepOrg')
        ]);
        if(!$stepOffice){
            $stepOffice = new Office();
            $stepOffice->setName('Home');
            $stepOffice->setOrganization($this->getReference('stepOrg'));
            $manager->persist($stepOffice);
        }

        $officeOne = $manager->getRepository('AppBundle:Office')->findOneBy([
            'name'=>'Office One',
            'organization' => $this->getReference('demoOrg')
        ]);
        if(!$officeOne){
            $officeOne = new Office();
            $officeOne->setName('Office One');
            $officeOne->setOrganization($this->getReference('demoOrg'));
            $manager->persist($officeOne);
        }

        $officeTwo = $manager->getRepository('AppBundle:Office')->findOneBy([
            'name'=>'Office Two',
            'organization' => $this->getReference('demoOrg')
        ]);
        if(!$officeTwo){
            $officeTwo = new Office();
            $officeTwo->setName('Office Two');
            $officeTwo->setOrganization($this->getReference('demoOrg'));
            $manager->persist($officeTwo);
        }

        $manager->flush();

        $this->addReference('stepOffice', $stepOffice);
        $this->addReference('officeOne', $officeOne);
        $this->addReference('officeTwo', $officeTwo);

        $aclProvider = $this->container->get('security.acl.provider');
        $devRoleSecurityIdentity = new RoleSecurityIdentity('ROLE_DEV');
        $adminRoleSecurityIdentity = new RoleSecurityIdentity('ROLE_ADMIN');
        $leadRoleSecurityIdentity = new RoleSecurityIdentity('ROLE_LEAD');
        $userRoleSecurityIdentity = new RoleSecurityIdentity('ROLE_USER');

        $objectIdentity = ObjectIdentity::fromDomainObject($stepOffice);
        try {
            $acl = $aclProvider->findAcl($objectIdentity);
        } catch (\Symfony\Component\Security\Acl\Exception\AclNotFoundException $e) {
            $acl = $aclProvider->createAcl($objectIdentity);
            $acl->insertObjectAce($userRoleSecurityIdentity, MaskBuilder::MASK_VIEW);
            $acl->insertObjectAce($adminRoleSecurityIdentity, MaskBuilder::MASK_OPERATOR);
            $aclProvider->updateAcl($acl);
        }

        $objectIdentity = ObjectIdentity::fromDomainObject($officeOne);
        try {
            $acl = $aclProvider->findAcl($objectIdentity);
        } catch (\Symfony\Component\Security\Acl\Exception\AclNotFoundException $e) {
            $acl = $aclProvider->createAcl($objectIdentity);
            $acl->insertObjectAce($userRoleSecurityIdentity, MaskBuilder::MASK_VIEW);
            $acl->insertObjectAce($adminRoleSecurityIdentity, MaskBuilder::MASK_OPERATOR);
            $aclProvider->updateAcl($acl);
        }

        $objectIdentity = ObjectIdentity::fromDomainObject($officeTwo);
        try {
            $acl = $aclProvider->findAcl($objectIdentity);
        } catch (\Symfony\Component\Security\Acl\Exception\AclNotFoundException $e) {
            $acl = $aclProvider->createAcl($objectIdentity);
            $acl->insertObjectAce($userRoleSecurityIdentity, MaskBuilder::MASK_VIEW);
            $acl->insertObjectAce($adminRoleSecurityIdentity, MaskBuilder::MASK_OPERATOR);
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

    /**
     * {@inheritDoc}
     */
    public function getDependencies()
    {
        return ['AppBundle\DataFixtures\ORM\LoadOrganizationData']; // fixture classes fixture is dependent on
    }

}