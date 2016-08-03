<?php

namespace AppBundle\Controller\Mixin;

use Symfony\Component\HttpFoundation\Request;


trait WampUpdatePusher
{
    private function pushUpdate($entity)
    {
        $client = $this->container->get('thruway.client');
        $serializer = $this->get('jms_serializer');

        $entityReflectiion = new \ReflectionClass(get_class($entity));
        $classShortName = strtolower($entityReflectiion->getShortName());
        $json = $serializer->serialize($entity, 'json');

        $client->publish("com.stepthrough."+$classShortName, [$json]);
    }
}