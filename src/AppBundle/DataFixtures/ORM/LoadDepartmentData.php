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

        $oneOne = new Department();
        $oneOne->setName('ONE-DeptOne');
        $oneOne->setOffice($this->getReference('officeOne'));
        $manager->persist($oneOne);

        $oneTwo = new Department();
        $oneTwo->setName('ONE-DeptTwo');
        $oneTwo->setOffice($this->getReference('officeOne'));
        $manager->persist($oneTwo);

        $oneThree = new Department();
        $oneThree->setName('ONE-DeptThree');
        $oneThree->setOffice($this->getReference('officeOne'));
        $manager->persist($oneThree);

        $twoOne = new Department();
        $twoOne->setName('TWO-DeptOne');
        $twoOne->setOffice($this->getReference('officeTwo'));
        $manager->persist($twoOne);

        $twoTwo = new Department();
        $twoTwo->setName('TWO-DeptTwo');
        $twoTwo->setOffice($this->getReference('officeTwo'));
        $manager->persist($twoTwo);

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

        $objectIdentity = ObjectIdentity::fromDomainObject($oneOne);
        $acl = $aclProvider->createAcl($objectIdentity);
        $acl->insertObjectAce($userRoleSecurityIdentity, MaskBuilder::MASK_VIEW);
        $acl->insertObjectAce($adminRoleSecurityIdentity, MaskBuilder::MASK_OPERATOR);
        $aclProvider->updateAcl($acl);

        $objectIdentity = ObjectIdentity::fromDomainObject($oneTwo);
        $acl = $aclProvider->createAcl($objectIdentity);
        $acl->insertObjectAce($adminRoleSecurityIdentity, MaskBuilder::MASK_VIEW);
        $acl->insertObjectAce($adminRoleSecurityIdentity, MaskBuilder::MASK_OPERATOR);
        $aclProvider->updateAcl($acl);

        $objectIdentity = ObjectIdentity::fromDomainObject($oneThree);
        $acl = $aclProvider->createAcl($objectIdentity);
        $acl->insertObjectAce($leadRoleSecurityIdentity, MaskBuilder::MASK_VIEW);
        $acl->insertObjectAce($adminRoleSecurityIdentity, MaskBuilder::MASK_OPERATOR);
        $aclProvider->updateAcl($acl);

        $objectIdentity = ObjectIdentity::fromDomainObject($twoOne);
        $acl = $aclProvider->createAcl($objectIdentity);
        $acl->insertObjectAce($userRoleSecurityIdentity, MaskBuilder::MASK_VIEW);
        $acl->insertObjectAce($adminRoleSecurityIdentity, MaskBuilder::MASK_OPERATOR);
        $aclProvider->updateAcl($acl);

        $objectIdentity = ObjectIdentity::fromDomainObject($twoTwo);
        $acl = $aclProvider->createAcl($objectIdentity);
        $acl->insertObjectAce($userRoleSecurityIdentity, MaskBuilder::MASK_VIEW);
        $acl->insertObjectAce($adminRoleSecurityIdentity, MaskBuilder::MASK_OPERATOR);
        $aclProvider->updateAcl($acl);


        $this->addReference('stepDept', $stepDept);
        $this->addReference('oneOne', $oneOne);
        $this->addReference('oneTwo', $oneTwo);
        $this->addReference('oneThree', $oneThree);
        $this->addReference('twoOne', $twoOne);
        $this->addReference('twoTwo', $twoTwo);

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