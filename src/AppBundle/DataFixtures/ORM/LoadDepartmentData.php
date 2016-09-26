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
use AppBundle\Entity\Department;

class LoadDepartmentData extends AbstractFixture implements DependentFixtureInterface, ContainerAwareInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $stepDept = new Department();
        $stepDept->setName('Home');
        $stepDept->setOffice($this->getReference('stepOffice'));
        $manager->persist($stepDept);

        $dfwCheckin = new Department();
        $dfwCheckin->setName('DFW-Check-In');
        $dfwCheckin->setOffice($this->getReference('dfwOffice'));
        $manager->persist($dfwCheckin);

        $dfwProcessing = new Department();
        $dfwProcessing->setName('DFW-Processing');
        $dfwProcessing->setOffice($this->getReference('dfwOffice'));
        $manager->persist($dfwProcessing);

        $dfwShipping = new Department();
        $dfwShipping->setName('DFW-Shipping');
        $dfwShipping->setOffice($this->getReference('dfwOffice'));
        $manager->persist($dfwShipping);

        $ausCheckin = new Department();
        $ausCheckin->setName('AUS-Check-In');
        $ausCheckin->setOffice($this->getReference('ausOffice'));
        $manager->persist($ausCheckin);

        $ausShipping = new Department();
        $ausShipping->setName('AUS-Shipping');
        $ausShipping->setOffice($this->getReference('ausOffice'));
        $manager->persist($ausShipping);

        $manager->flush();

        $aclProvider = $this->container->get('security.acl.provider');

        $devRoleSecurityIdentity = new RoleSecurityIdentity('ROLE_DEV');
        $adminRoleSecurityIdentity = new RoleSecurityIdentity('ROLE_ADMIN');
        $leadRoleSecurityIdentity = new RoleSecurityIdentity('ROLE_LEAD');
        $userRoleSecurityIdentity = new RoleSecurityIdentity('ROLE_USER');

        $objectIdentity = ObjectIdentity::fromDomainObject($stepDept);
        $acl = $aclProvider->createAcl($objectIdentity);
        $acl->insertObjectAce($userRoleSecurityIdentity, MaskBuilder::MASK_VIEW);
        $acl->insertObjectAce($adminRoleSecurityIdentity, MaskBuilder::MASK_OPERATOR);
        $aclProvider->updateAcl($acl);

        $objectIdentity = ObjectIdentity::fromDomainObject($dfwCheckin);
        $acl = $aclProvider->createAcl($objectIdentity);
        $acl->insertObjectAce($userRoleSecurityIdentity, MaskBuilder::MASK_VIEW);
        $acl->insertObjectAce($adminRoleSecurityIdentity, MaskBuilder::MASK_OPERATOR);
        $aclProvider->updateAcl($acl);

        $objectIdentity = ObjectIdentity::fromDomainObject($dfwProcessing);
        $acl = $aclProvider->createAcl($objectIdentity);
        $acl->insertObjectAce($adminRoleSecurityIdentity, MaskBuilder::MASK_VIEW);
        $acl->insertObjectAce($adminRoleSecurityIdentity, MaskBuilder::MASK_OPERATOR);
        $aclProvider->updateAcl($acl);

        $objectIdentity = ObjectIdentity::fromDomainObject($dfwShipping);
        $acl = $aclProvider->createAcl($objectIdentity);
        $acl->insertObjectAce($leadRoleSecurityIdentity, MaskBuilder::MASK_VIEW);
        $acl->insertObjectAce($adminRoleSecurityIdentity, MaskBuilder::MASK_OPERATOR);
        $aclProvider->updateAcl($acl);

        $objectIdentity = ObjectIdentity::fromDomainObject($ausCheckin);
        $acl = $aclProvider->createAcl($objectIdentity);
        $acl->insertObjectAce($userRoleSecurityIdentity, MaskBuilder::MASK_VIEW);
        $acl->insertObjectAce($adminRoleSecurityIdentity, MaskBuilder::MASK_OPERATOR);
        $aclProvider->updateAcl($acl);

        $objectIdentity = ObjectIdentity::fromDomainObject($ausShipping);
        $acl = $aclProvider->createAcl($objectIdentity);
        $acl->insertObjectAce($userRoleSecurityIdentity, MaskBuilder::MASK_VIEW);
        $acl->insertObjectAce($adminRoleSecurityIdentity, MaskBuilder::MASK_OPERATOR);
        $aclProvider->updateAcl($acl);


        $this->addReference('stepDept', $stepDept);
        $this->addReference('dfwCheckin', $dfwCheckin);
        $this->addReference('dfwProcessing', $dfwProcessing);
        $this->addReference('dfwShipping', $dfwShipping);
        $this->addReference('ausCheckin', $ausCheckin);
        $this->addReference('ausShipping', $ausShipping);

    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies()
    {
        return ['AppBundle\DataFixtures\ORM\LoadOfficeData']; // fixture classes fixture is dependent on
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

//$userGroupAdmin->setUser($this->getReference('admin-user'));