<?php
namespace AppBundle\Library\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractService
{
    /** @var Symfony\Component\DependencyInjection\ContainerInterface */
    protected $container;
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    protected function getEntityManager()
    {
        return $this->container
            ->get('doctrine')
            ->getManager();
    }
}