<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use AppBundle\Entity\Organization;

class LoadOrganizationData extends AbstractFixture implements ContainerAwareInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $createdEntities = [];
        $stepOrg = $manager->getRepository('AppBundle:SingleQueryReport')->findOneBy(['name'=>'Step Inventory']);
        if(!$stepOrg){
            $stepOrg = new Organization();
            $stepOrg->setName('Step Inventory');
            $manager->persist($stepOrg);
            $createdEntities['stepOrg'] = true;
        }

        $demoOrg = $manager->getRepository('AppBundle:SingleQueryReport')->findOneBy(['name'=>'Acme Inc.']);
        if(!$demoOrg){
            $demoOrg = new Organization();
            $demoOrg->setName('Acme Inc.');
            $manager->persist($demoOrg);
            $createdEntities['demoOrg'] = true;
        }

        $manager->flush();

        $this->addReference('stepOrg', $stepOrg);
        $this->addReference('demoOrg', $demoOrg);

        $aclProvider = $this->container->get('security.acl.provider');
        $devRoleSecurityIdentity = new RoleSecurityIdentity('ROLE_DEV');
        $adminRoleSecurityIdentity = new RoleSecurityIdentity('ROLE_ADMIN');
        $leadRoleSecurityIdentity = new RoleSecurityIdentity('ROLE_LEAD');
        $userRoleSecurityIdentity = new RoleSecurityIdentity('ROLE_USER');

        if(isset($createdEntities['stepOrg']) and $createdEntities['stepOrg']){
            $objectIdentity = ObjectIdentity::fromDomainObject($stepOrg);
            $acl = $aclProvider->createAcl($objectIdentity);
            $acl->insertObjectAce($userRoleSecurityIdentity, MaskBuilder::MASK_VIEW);
            $acl->insertObjectAce($devRoleSecurityIdentity, MaskBuilder::MASK_OPERATOR);
            $aclProvider->updateAcl($acl);
        }

        if(isset($createdEntities['demoOrg']) and $createdEntities['demoOrg']){
            $objectIdentity = ObjectIdentity::fromDomainObject($demoOrg);
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

}