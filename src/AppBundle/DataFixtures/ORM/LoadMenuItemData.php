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
use AppBundle\Entity\MenuItem;

class LoadMenuItemData extends AbstractFixture implements DependentFixtureInterface, ContainerAwareInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {

        $refs = $this->referenceRepository->getReferences();
        $orgRefNames = [];
        $deptRefNames = [];
        $linkRefNames = [];

        foreach($refs as $ref){
            $refNames = $this->referenceRepository->getReferenceNames($ref);
            if(is_a($ref, 'AppBundle\Entity\Department')){
                $deptRefNames[] = $refNames[0];
            }
            if(is_a($ref, 'AppBundle\Entity\MenuLink')){
                $linkRefNames[] = $refNames[0];
            }
        }
        $items = [];
        foreach($deptRefNames as $deptRefName){
            $i=1;
            $department = $this->getReference($deptRefName);
            foreach($linkRefNames as $linkRefName){
                $search = [
                    'position' => $i,
                    'menuLink'=>$this->getReference($linkRefName),
                    'organization' => $department->getOffice()->getOrganization(),
                ];
                if(in_array($linkRefName, ['inventoryAuditLink','inventoryActionLink', 'inventoryLogLink'])){
                    $search['parent'] = $items[$deptRefName]['mainLink'];
                }else if(in_array($linkRefName, ['adminInventoryLink','adminAccountingLink'])){
                    $search['parent'] = $items[$deptRefName]['adminLink'];
                }else{
                    $search['department'] = $department;
                }
                $item = $manager->getRepository('AppBundle:MenuItem')->findOneBy($search);
                if(!$item){
                    $item = new MenuItem();
                    $item->isActive(true);
                    $item->setPosition($i);
                    $item->setMenuLink($this->getReference($linkRefName));
                    $item->setOrganization($department->getOffice()->getOrganization());
                    if(in_array($linkRefName, ['inventoryAuditLink','inventoryActionLink', 'inventoryLogLink'])){
                        $item->setParent($items[$deptRefName]['mainLink']);
                    }else if(in_array($linkRefName, ['adminInventoryLink','adminAccountingLink'])){
                        $item->setParent($items[$deptRefName]['adminLink']);
                    }else{
                        $item->setDepartment($department);
                    }
                    $manager->persist($item);
                }
                $items[$deptRefName][$linkRefName] = $item;
                $i++;
            }
        }


        $manager->flush();

        $aclProvider = $this->container->get('security.acl.provider');
        $devRoleSecurityIdentity = new RoleSecurityIdentity('ROLE_DEV');
        $adminRoleSecurityIdentity = new RoleSecurityIdentity('ROLE_ADMIN');
        $leadRoleSecurityIdentity = new RoleSecurityIdentity('ROLE_LEAD');
        $userRoleSecurityIdentity = new RoleSecurityIdentity('ROLE_USER');

        foreach($items as $deptItems){
            foreach($deptItems as $item){
                $objectIdentity = ObjectIdentity::fromDomainObject($item);
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

    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies()
    {
        return ['AppBundle\DataFixtures\ORM\LoadOrganizationData','AppBundle\DataFixtures\ORM\LoadMenuLinkData','AppBundle\DataFixtures\ORM\LoadDepartmentData']; // fixture classes fixture is dependent on
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