<?php

namespace AppBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class AnnouncementRepository extends EntityRepository
{
    public function findLatest(Organization $organization)
    {
        return $this
            ->createQueryBuilder('a')
            ->select('a')
            ->where('a.organization = :org or a.organization is null')
            ->andWhere('a.isActive = :true')
            ->setParameter(':org', $organization)
            ->setParameter(':true', true)
            ->orderBy('a.postedAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

}