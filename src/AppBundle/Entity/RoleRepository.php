<?php

namespace AppBundle\Entity;


use Doctrine\ORM\EntityRepository;

class RoleRepository extends EntityRepository
{
    public function fetchRoleHierarchy()
    {
        $q = $this
            ->createQueryBuilder('r')
            ->select('r.role role, rh.role child_role')
            ->leftJoin('r.roleHierarchy', 'rh')
            ->getQuery();
        
        return $q->getResult();
    }
}