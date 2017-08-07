<?php

namespace AppBundle\Entity;


use Doctrine\ORM\EntityRepository;

class BinRepository extends EntityRepository
{
    public function findDeviationBin(Bin $bin)
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('b')
            ->from('AppBundle:Bin', 'b')
            ->join('b.department', 'd')
            ->where('b.name LIKE :deviation_name')
            ->andWhere('d.office = :office')
            ->setParameter('deviation_name', '%Deviation')
            ->setParameter('office', $bin->getDepartment()->getOffice())
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findShippedBin(Organization $organization)
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('b')
            ->from('AppBundle:Bin', 'b')
            ->join('b.department', 'd')
            ->join('d.office', 'o')
            ->where('b.name LIKE :deviation_name')
            ->andWhere('o.organization = :org')
            ->setParameter('deviation_name', 'Shipped%')
            ->setParameter('org', $organization)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}