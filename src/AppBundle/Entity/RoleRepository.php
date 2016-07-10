<?php

namespace AppBundle\Entity;


use Doctrine\ORM\EntityRepository;

class RoleRepository extends EntityRepository
{
    public function fetchRoleHierarchy()
    {
        return  $this->getEntityManager()->createQueryBuilder()
            ->select('rr')
            ->from('AppBundle:RoleRole', 'rr')
            ->getQuery()
            ->getResult();
    }
}