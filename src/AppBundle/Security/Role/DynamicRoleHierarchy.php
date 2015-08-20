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

    protected function fetchRoleHierarchy()
    {
        $hierarchy = array();
        
        foreach($this->roleRepository->fetchRoleHierarchy() as $row){
            if( ! isset($hierarchy[$row['role']]) ){
                $hierarchy[$row['role']] = array($row['role']);
            }
            if($row['child_role']){
                $hierarchy[$row['role']][] = $row['child_role'];
            }
        }

        return $hierarchy;
    }
}