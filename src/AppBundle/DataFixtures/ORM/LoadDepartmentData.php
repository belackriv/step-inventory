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
        $stepDept = $manager->getRepository('AppBundle:Department')->findOneBy([
            'name'=>'Home',
            'office' => $this->getReference('stepOffice')
        ]);
        if(!$stepDept){
            $stepDept = new Department();
            $stepDept->setName('Home');
            $stepDept->setOffice($this->getReference('stepOffice'));
            $manager->persist($stepDept);
        }

        $oneOne = $manager->getRepository('AppBundle:Department')->findOneBy([
            'name'=>'ONE-DeptOne',
            'office' => $this->getReference('officeOne')
        ]);
        if(!$oneOne){
            $oneOne = new Department();
            $oneOne->setName('ONE-DeptOne');
            $oneOne->setOffice($this->getReference('officeOne'));
            $manager->persist($oneOne);
        }

        $oneTwo = $manager->getRepository('AppBundle:Department')->findOneBy([
            'name'=>'ONE-DeptTwo',
            'office' => $this->getReference('officeOne')
        ]);
        if(!$oneTwo){
            $oneTwo = new Department();
            $oneTwo->setName('ONE-DeptTwo');
            $oneTwo->setOffice($this->getReference('officeOne'));
            $manager->persist($oneTwo);
        }

        $oneThree = $manager->getRepository('AppBundle:Department')->findOneBy([
            'name'=>'ONE-DeptThree',
            'office' => $this->getReference('officeOne')
        ]);
        if(!$oneThree){
            $oneThree = new Department();
            $oneThree->setName('ONE-DeptThree');
            $oneThree->setOffice($this->getReference('officeOne'));
            $manager->persist($oneThree);
        }

        $twoOne = $manager->getRepository('AppBundle:Department')->findOneBy([
            'name'=>'TWO-DeptOne',
            'office' => $this->getReference('officeTwo')
        ]);
        if(!$twoOne){
            $twoOne = new Department();
            $twoOne->setName('TWO-DeptOne');
            $twoOne->setOffice($this->getReference('officeTwo'));
            $manager->persist($twoOne);
        }

        $twoTwo = $manager->getRepository('AppBundle:Department')->findOneBy([
            'name'=>'TWO-DeptTwo',
            'office' => $this->getReference('officeTwo')
        ]);
        if(!$twoTwo){
            $twoTwo = new Department();
            $twoTwo->setName('TWO-DeptTwo');
            $twoTwo->setOffice($this->getReference('officeTwo'));
            $manager->persist($twoTwo);
        }

        $manager->flush();

        $aclProvider = $this->container->get('security.acl.provider');

        $devRoleSecurityIdentity = new RoleSecurityIdentity('ROLE_DEV');
        $adminRoleSecurityIdentity = new RoleSecurityIdentity('ROLE_ADMIN');
        $leadRoleSecurityIdentity = new RoleSecurityIdentity('ROLE_LEAD');
        $userRoleSecurityIdentity = new RoleSecurityIdentity('ROLE_USER');

        $objectIdentity = ObjectIdentity::fromDomainObject($stepDept);
        try {
            $acl = $aclProvider->findAcl($objectIdentity);
        } catch (\Symfony\Component\Security\Acl\Exception\AclNotFoundException $e) {
            $acl = $aclProvider->createAcl($objectIdentity);
            $acl->insertObjectAce($userRoleSecurityIdentity, MaskBuilder::MASK_VIEW);
            $acl->insertObjectAce($adminRoleSecurityIdentity, MaskBuilder::MASK_OPERATOR);
            $aclProvider->updateAcl($acl);
        }

        $objectIdentity = ObjectIdentity::fromDomainObject($oneOne);
        try {
            $acl = $aclProvider->findAcl($objectIdentity);
        } catch (\Symfony\Component\Security\Acl\Exception\AclNotFoundException $e) {
            $acl = $aclProvider->createAcl($objectIdentity);
            $acl->insertObjectAce($userRoleSecurityIdentity, MaskBuilder::MASK_VIEW);
            $acl->insertObjectAce($adminRoleSecurityIdentity, MaskBuilder::MASK_OPERATOR);
            $aclProvider->updateAcl($acl);
        }

        $objectIdentity = ObjectIdentity::fromDomainObject($oneTwo);
        try {
            $acl = $aclProvider->findAcl($objectIdentity);
        } catch (\Symfony\Component\Security\Acl\Exception\AclNotFoundException $e) {
            $acl = $aclProvider->createAcl($objectIdentity);
            $acl->insertObjectAce($adminRoleSecurityIdentity, MaskBuilder::MASK_VIEW);
            $acl->insertObjectAce($adminRoleSecurityIdentity, MaskBuilder::MASK_OPERATOR);
            $aclProvider->updateAcl($acl);
        }

        $objectIdentity = ObjectIdentity::fromDomainObject($oneThree);
        try {
            $acl = $aclProvider->findAcl($objectIdentity);
        } catch (\Symfony\Component\Security\Acl\Exception\AclNotFoundException $e) {
            $acl = $aclProvider->createAcl($objectIdentity);
            $acl->insertObjectAce($leadRoleSecurityIdentity, MaskBuilder::MASK_VIEW);
            $acl->insertObjectAce($adminRoleSecurityIdentity, MaskBuilder::MASK_OPERATOR);
            $aclProvider->updateAcl($acl);
        }

        $objectIdentity = ObjectIdentity::fromDomainObject($twoOne);
        try {
            $acl = $aclProvider->findAcl($objectIdentity);
        } catch (\Symfony\Component\Security\Acl\Exception\AclNotFoundException $e) {
            $acl = $aclProvider->createAcl($objectIdentity);
            $acl->insertObjectAce($userRoleSecurityIdentity, MaskBuilder::MASK_VIEW);
            $acl->insertObjectAce($adminRoleSecurityIdentity, MaskBuilder::MASK_OPERATOR);
            $aclProvider->updateAcl($acl);
        }

        $objectIdentity = ObjectIdentity::fromDomainObject($twoTwo);
        try {
            $acl = $aclProvider->findAcl($objectIdentity);
        } catch (\Symfony\Component\Security\Acl\Exception\AclNotFoundException $e) {
            $acl = $aclProvider->createAcl($objectIdentity);
            $acl->insertObjectAce($userRoleSecurityIdentity, MaskBuilder::MASK_VIEW);
            $acl->insertObjectAce($adminRoleSecurityIdentity, MaskBuilder::MASK_OPERATOR);
            $aclProvider->updateAcl($acl);
        }

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