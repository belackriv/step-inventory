<?php
namespace AppBundle\Security\Role;

use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchy;

use AppBundle\Entity\RoleRepository;

class DynamicRoleHierarchy implements RoleHierarchyInterface
{
    protected $roleRepository;
    protected $roleHierarchy = null;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function getReachableRoles(array $roles)
    {
        if (null === $this->roleHierarchy) {
            $this->roleHierarchy = new RoleHierarchy($this->fetchRoleHierarchy());
        }

        return $this->roleHierarchy->getReachableRoles($roles);
    }

    public function fetchRoleHierarchy()
    {
        $hierarchy = array();
        $roleHierarchy = $this->roleRepository->fetchRoleHierarchy();
        foreach($roleHierarchy as $roleRole){
            $roleSourceName = $roleRole->getRoleSource()->getRole();
            $roleTargetName = $roleRole->getRoleTarget()->getRole();
            if( !isset($hierarchy[$roleSourceName]) ){
                $hierarchy[$roleSourceName] = [$roleTargetName];
            }else{
                if(!in_array($roleTargetName, $hierarchy[$roleSourceName])){
                    $hierarchy[$roleSourceName][] = $roleTargetName;
                }
            }
        }
        return $hierarchy;
    }

}