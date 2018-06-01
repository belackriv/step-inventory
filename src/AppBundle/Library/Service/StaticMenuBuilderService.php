<?php
namespace AppBundle\Library\Service;

use AppBundle\Entity\MenuLink;
use AppBundle\Entity\MenuItem;

class StaticMenuBuilderService
{
    use \Symfony\Component\DependencyInjection\ContainerAwareTrait;

    public static $menuLinkRoles = [
        'Main' => 'ROLE_USER',
        'Inventory Actions' => 'ROLE_USER',
        'Inventory Logs' => 'ROLE_USER',
        'Inventory Audit' => 'ROLE_LEAD',
        'Admin Options' => 'ROLE_ADMIN',
        'Admin Inventory' => 'ROLE_ADMIN',
        'Admin Accounting' => 'ROLE_ADMIN',
        'Reporting' => 'ROLE_USER'
    ];

    public static $menuItemListDefinitions = [
        [
          'linkName' => 'Main',
          'children' => [ ['linkName' => 'Inventory Actions'], ['linkName' => 'Inventory Logs'], ['linkName' => 'Inventory Audit']]
        ],
        [
          'linkName' => 'Admin Options',
          'children' => [ ['linkName' => 'Admin Inventory'], ['linkName' => 'Admin Accounting']]
        ],
        [
          'linkName' => 'Reporting',
          'children' => []
        ]
    ];

    private $menuLinks = [];

	public function build()
    {
        return $this->buildTree(self::$menuItemListDefinitions);
    }

    private function buildTree(array $itemDefs)
    {
        $menuItems = [];
        $user = $this->container->get('security.token_storage')
            ->getToken()
            ->getUser();
        $roles = $this->container->get('security.role_hierarchy')->getReachableRoles($user->getRolesAsRoles());
        $rolesAsString = [];
        foreach($roles as $role){
            $rolesAsString[] = $role->getRole();
        }
        foreach($itemDefs as $index => $itemDef){
            if(in_array(self::$menuLinkRoles[$itemDef['linkName']], $rolesAsString) === true) {
                $menuLink = $this->findMenuLinkByName($itemDef['linkName']);
                $children = [];
                if(array_key_exists('children', $itemDef)){
                    $children = $this->buildTree($itemDef['children']);
                }

                $menuItem = new MenuItem;
                $menuItem->setIsActive(true);
                $menuItem->setPosition($index + 1);
                $menuItem->setMenuLink($menuLink);
                foreach($children as $child){
                    $menuItem->addChild($child);
                }
                $menuItems[] = $menuItem;
            }
        }
        return $menuItems;
    }

    private function findMenuLinkByName($name)
    {
        if(count($this->menuLinks) < 1){
            $this->menuLinks = $this->container->get('doctrine')
                ->getRepository('AppBundle:MenuLink')
                ->findAll();
        }
        foreach($this->menuLinks as $menuLink){
            if($menuLink->getName() === $name){
                return $menuLink;
            }
        }
        return null;
    }

}