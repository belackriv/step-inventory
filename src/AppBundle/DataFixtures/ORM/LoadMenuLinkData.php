<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use AppBundle\Entity\MenuLink;

class LoadMenuLinkData extends AbstractFixture implements ContainerAwareInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {

        $mainLink = new MenuLink();
        $mainLink->setName('Main');
        $mainLink->setUrl(null);
        $manager->persist($mainLink);

        $inventoryLink = new MenuLink();
        $inventoryLink->setName('Inventory');
        $inventoryLink->setUrl('/inventory');
        $inventoryLink->setRouteMatches(['bin_part_count','inventory_part_adjustment','inventory_part_movement']);
        $manager->persist($inventoryLink);

        $inventoryAuditLink = new MenuLink();
        $inventoryAuditLink->setName('Inventory Audit');
        $inventoryAuditLink->setUrl('/inventory_audit');
        $manager->persist($inventoryAuditLink);

        $adminLink = new MenuLink();
        $adminLink->setName('Admin Options');
        $adminLink->setUrl('/admin');
        $adminLink->setRouteMatches(['user','menu_item']);
        $manager->persist($adminLink);

        $adminInventoryLink = new MenuLink();
        $adminInventoryLink->setName('Admin Inventory');
        $adminInventoryLink->setUrl('/admin_inventory');
        $adminInventoryLink->setRouteMatches(['part','part_category','part_group','bin','bin_type','inventory_movement_rule']);
        $manager->persist($adminInventoryLink);

        $adminAccountingLink = new MenuLink();
        $adminAccountingLink->setName('Admin Accounting');
        $adminAccountingLink->setUrl('/admin_accounting');
        $adminAccountingLink->setRouteMatches(['client','customer','inbound_order','outbound_order']);
        $manager->persist($adminAccountingLink);


        $manager->flush();

        $aclProvider = $this->container->get('security.acl.provider');

        $devRoleSecurityIdentity = new RoleSecurityIdentity('ROLE_DEV');
        $adminRoleSecurityIdentity = new RoleSecurityIdentity('ROLE_ADMIN');
        $leadRoleSecurityIdentity = new RoleSecurityIdentity('ROLE_LEAD');
        $userRoleSecurityIdentity = new RoleSecurityIdentity('ROLE_USER');

        $objectIdentity = ObjectIdentity::fromDomainObject($mainLink);
        $acl = $aclProvider->createAcl($objectIdentity);
        $acl->insertObjectAce($userRoleSecurityIdentity, MaskBuilder::MASK_VIEW);
        $acl->insertObjectAce($devRoleSecurityIdentity, MaskBuilder::MASK_OPERATOR);
        $aclProvider->updateAcl($acl);

        $objectIdentity = ObjectIdentity::fromDomainObject($inventoryLink);
        $acl = $aclProvider->createAcl($objectIdentity);
        $acl->insertObjectAce($userRoleSecurityIdentity, MaskBuilder::MASK_VIEW);
        $acl->insertObjectAce($devRoleSecurityIdentity, MaskBuilder::MASK_OPERATOR);
        $aclProvider->updateAcl($acl);

        $objectIdentity = ObjectIdentity::fromDomainObject($inventoryAuditLink);
        $acl = $aclProvider->createAcl($objectIdentity);
        $acl->insertObjectAce($leadRoleSecurityIdentity, MaskBuilder::MASK_VIEW);
        $acl->insertObjectAce($devRoleSecurityIdentity, MaskBuilder::MASK_OPERATOR);
        $aclProvider->updateAcl($acl);

        $objectIdentity = ObjectIdentity::fromDomainObject($adminLink);
        $acl = $aclProvider->createAcl($objectIdentity);
        $acl->insertObjectAce($adminRoleSecurityIdentity, MaskBuilder::MASK_VIEW);
        $acl->insertObjectAce($devRoleSecurityIdentity, MaskBuilder::MASK_OPERATOR);
        $aclProvider->updateAcl($acl);

        $objectIdentity = ObjectIdentity::fromDomainObject($adminInventoryLink);
        $acl = $aclProvider->createAcl($objectIdentity);
        $acl->insertObjectAce($adminRoleSecurityIdentity, MaskBuilder::MASK_VIEW);
        $acl->insertObjectAce($devRoleSecurityIdentity, MaskBuilder::MASK_OPERATOR);
        $aclProvider->updateAcl($acl);



        $this->addReference('mainLink', $mainLink);
        $this->addReference('inventoryLink', $inventoryLink);
        $this->addReference('inventoryAuditLink', $inventoryAuditLink);
        $this->addReference('adminLink', $adminLink);
        $this->addReference('adminInventoryLink', $adminInventoryLink);

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