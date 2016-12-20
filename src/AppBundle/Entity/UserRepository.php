<?php

namespace AppBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class UserRepository extends EntityRepository implements UserProviderInterface, UserLoaderInterface
{
    public function loadUserByUsername($username)
    {
        $query = $this
            ->createQueryBuilder('u')
            ->select('u')
            ->where('lower(u.username) = :username OR lower(u.email) = :email')
            ->setParameter('username', strtolower($username))
            ->setParameter('email', strtolower($username))
            ->getQuery();

        try {
            // The Query::getSingleResult() method throws an exception
            // if there is no record matching the criteria.
            $user = $query->getSingleResult();
        } catch (NoResultException $e) {
            $message = sprintf(
                'Unable to find an user identified by "%s".',
                $username
            );
            throw new UsernameNotFoundException($message, 0, $e);
        }

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(
                sprintf(
                    'Instances of "%s" are not supported.',
                    $class
                )
            );
        }

        return $this->find($user->getId());
    }

    public function supportsClass($class)
    {

        return $this->getEntityName() === $class
            || is_subclass_of($class, $this->getEntityName());
    }

    public function searchUsers($criteria)
    {
        $qb = $this->createQueryBuilder('u')->select('u');
        $metadata = $this->getClassMetadata();
        $i = 1;
        foreach($criteria as $field => $criterion){
            $fieldType = $metadata->getTypeOfField($field);
            if($fieldType === 'string'){
                $qb->add('where', $qb->expr()->andX(
                    $qb->expr()->like('u.'.$field, '?'.$i)
                ))->setParameter($i, '%'.$criterion.'%');
            }
            $i++;
        }

        return $qb->getQuery()->getResult();
    }
}