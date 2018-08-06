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
        $mainLink = $manager->getRepository('AppBundle:MenuLink')->findOneBy(['name'=>'Main']);
        if(!$mainLink){
            $mainLink = new MenuLink();
            $mainLink->setName('Main');
            $mainLink->setUrl(null);
            $manager->persist($mainLink);
        }

        $inventoryActionLink = $manager->getRepository('AppBundle:MenuLink')->findOneBy(['name'=>'Inventory Actions']);
        if(!$inventoryActionLink){
            $inventoryActionLink = new MenuLink();
            $inventoryActionLink->setName('Inventory Actions');
            $inventoryActionLink->setUrl('/inventory_action');
            $inventoryActionLink->setRouteMatches(['tid','bin_sku_count','sales_item','show/bin']);
            $manager->persist($inventoryActionLink);
        }

        $inventoryLogLink = $manager->getRepository('AppBundle:MenuLink')->findOneBy(['name'=>'Inventory Logs']);
        if(!$inventoryLogLink){
            $inventoryLogLink = new MenuLink();
            $inventoryLogLink->setName('Inventory Logs');
            $inventoryLogLink->setUrl('/inventory_log');
            $inventoryLogLink->setRouteMatches(['inventory_tid_edit','inventory_tid_movement','inventory_tid_transform','inventory_sku_adjustment','inventory_sku_movement','inventory_sku_transform']);
            $manager->persist($inventoryLogLink);
        }

        $inventoryAuditLink = $manager->getRepository('AppBundle:MenuLink')->findOneBy(['name'=>'Inventory Audit']);
        if(!$inventoryAuditLink){
            $inventoryAuditLink = new MenuLink();
            $inventoryAuditLink->setName('Inventory Audit');
            $inventoryAuditLink->setUrl('/inventory_audit');
            $manager->persist($inventoryAuditLink);
        }

        $adminLink = $manager->getRepository('AppBundle:MenuLink')->findOneBy(['name'=>'Admin Options']);
        if(!$adminLink){
            $adminLink = new MenuLink();
            $adminLink->setName('Admin Options');
            $adminLink->setUrl('/admin');
            $adminLink->setRouteMatches(['organization','user','office','department','menu_item','menu_link']);
            $manager->persist($adminLink);
        }

        $adminInventoryLink = $manager->getRepository('AppBundle:MenuLink')->findOneBy(['name'=>'Admin Inventory']);
        if(!$adminInventoryLink){
            $adminInventoryLink = new MenuLink();
            $adminInventoryLink->setName('Admin Inventory');
            $adminInventoryLink->setUrl('/admin_inventory');
            $adminInventoryLink->setRouteMatches(['part','part_category','part_group','bin','bin_type','inventory_movement_rule']);
            $manager->persist($adminInventoryLink);
        }

        $adminAccountingLink = $manager->getRepository('AppBundle:MenuLink')->findOneBy(['name'=>'Admin Accounting']);
        if(!$adminAccountingLink){
            $adminAccountingLink = new MenuLink();
            $adminAccountingLink->setName('Admin Accounting');
            $adminAccountingLink->setUrl('/admin_accounting');
            $adminAccountingLink->setRouteMatches(['client','customer','inbound_order','outbound_order']);
            $manager->persist($adminAccountingLink);
        }

        $reportingLink = $manager->getRepository('AppBundle:MenuLink')->findOneBy(['name'=>'Reporting']);
        if(!$reportingLink){
            $reportingLink = new MenuLink();
            $reportingLink->setName('Reporting');
            $reportingLink->setUrl('/reporting');
            $reportingLink->setRouteMatches(['reporting/single_query_report']);
            $manager->persist($reportingLink);
        }

        $manager->flush();

        $aclProvider = $this->container->get('security.acl.provider');

        $devRoleSecurityIdentity = new RoleSecurityIdentity('ROLE_DEV');
        $adminRoleSecurityIdentity = new RoleSecurityIdentity('ROLE_ADMIN');
        $leadRoleSecurityIdentity = new RoleSecurityIdentity('ROLE_LEAD');
        $userRoleSecurityIdentity = new RoleSecurityIdentity('ROLE_USER');


        $objectIdentity = ObjectIdentity::fromDomainObject($mainLink);
        try {
            $acl = $aclProvider->findAcl($objectIdentity);
        } catch (\Symfony\Component\Security\Acl\Exception\AclNotFoundException $e) {
            $acl = $aclProvider->createAcl($objectIdentity);
            $acl->insertObjectAce($userRoleSecurityIdentity, MaskBuilder::MASK_VIEW);
            $acl->insertObjectAce($devRoleSecurityIdentity, MaskBuilder::MASK_OPERATOR);
            $aclProvider->updateAcl($acl);
        }

        $objectIdentity = ObjectIdentity::fromDomainObject($inventoryActionLink);
        try {
            $acl = $aclProvider->findAcl($objectIdentity);
        } catch (\Symfony\Component\Security\Acl\Exception\AclNotFoundException $e) {
            $acl = $aclProvider->createAcl($objectIdentity);
            $acl->insertObjectAce($userRoleSecurityIdentity, MaskBuilder::MASK_VIEW);
            $acl->insertObjectAce($devRoleSecurityIdentity, MaskBuilder::MASK_OPERATOR);
            $aclProvider->updateAcl($acl);
        }

        $objectIdentity = ObjectIdentity::fromDomainObject($inventoryLogLink);
        try {
            $acl = $aclProvider->findAcl($objectIdentity);
        } catch (\Symfony\Component\Security\Acl\Exception\AclNotFoundException $e) {
            $acl = $aclProvider->createAcl($objectIdentity);
            $acl->insertObjectAce($userRoleSecurityIdentity, MaskBuilder::MASK_VIEW);
            $acl->insertObjectAce($devRoleSecurityIdentity, MaskBuilder::MASK_OPERATOR);
            $aclProvider->updateAcl($acl);
        }

        $objectIdentity = ObjectIdentity::fromDomainObject($inventoryAuditLink);
        try {
            $acl = $aclProvider->findAcl($objectIdentity);
        } catch (\Symfony\Component\Security\Acl\Exception\AclNotFoundException $e) {
            $acl = $aclProvider->createAcl($objectIdentity);
            $acl->insertObjectAce($leadRoleSecurityIdentity, MaskBuilder::MASK_VIEW);
            $acl->insertObjectAce($devRoleSecurityIdentity, MaskBuilder::MASK_OPERATOR);
            $aclProvider->updateAcl($acl);
        }

        $objectIdentity = ObjectIdentity::fromDomainObject($adminLink);
        try {
            $acl = $aclProvider->findAcl($objectIdentity);
        } catch (\Symfony\Component\Security\Acl\Exception\AclNotFoundException $e) {
            $acl = $aclProvider->createAcl($objectIdentity);
            $acl->insertObjectAce($adminRoleSecurityIdentity, MaskBuilder::MASK_VIEW);
            $acl->insertObjectAce($devRoleSecurityIdentity, MaskBuilder::MASK_OPERATOR);
            $aclProvider->updateAcl($acl);
        }

        $objectIdentity = ObjectIdentity::fromDomainObject($adminInventoryLink);
        try {
            $acl = $aclProvider->findAcl($objectIdentity);
        } catch (\Symfony\Component\Security\Acl\Exception\AclNotFoundException $e) {
            $acl = $aclProvider->createAcl($objectIdentity);
            $acl->insertObjectAce($adminRoleSecurityIdentity, MaskBuilder::MASK_VIEW);
            $acl->insertObjectAce($devRoleSecurityIdentity, MaskBuilder::MASK_OPERATOR);
            $aclProvider->updateAcl($acl);
        }

        $objectIdentity = ObjectIdentity::fromDomainObject($adminAccountingLink);
        try {
            $acl = $aclProvider->findAcl($objectIdentity);
        } catch (\Symfony\Component\Security\Acl\Exception\AclNotFoundException $e) {
            $acl = $aclProvider->createAcl($objectIdentity);
            $acl->insertObjectAce($adminRoleSecurityIdentity, MaskBuilder::MASK_VIEW);
            $acl->insertObjectAce($devRoleSecurityIdentity, MaskBuilder::MASK_OPERATOR);
            $aclProvider->updateAcl($acl);
        }

        $objectIdentity = ObjectIdentity::fromDomainObject($reportingLink);
        try {
            $acl = $aclProvider->findAcl($objectIdentity);
        } catch (\Symfony\Component\Security\Acl\Exception\AclNotFoundException $e) {
            $acl = $aclProvider->createAcl($objectIdentity);
            $acl->insertObjectAce($userRoleSecurityIdentity, MaskBuilder::MASK_VIEW);
            $acl->insertObjectAce($devRoleSecurityIdentity, MaskBuilder::MASK_OPERATOR);
            $aclProvider->updateAcl($acl);
        }

        $this->addReference('mainLink', $mainLink);
        $this->addReference('inventoryActionLink', $inventoryActionLink);
        $this->addReference('inventoryLogLink', $inventoryLogLink);
        $this->addReference('inventoryAuditLink', $inventoryAuditLink);
        $this->addReference('adminLink', $adminLink);
        $this->addReference('adminInventoryLink', $adminInventoryLink);
        $this->addReference('adminAccountingLink', $adminAccountingLink);
        $this->addReference('reportingLink', $reportingLink);

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