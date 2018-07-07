<?php

/*
 * Copyright 2013 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace AppBundle\Serializer\Handler;

use AppBundle\Entity\TravelerId;
use AppBundle\Entity\SalesItem;

use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symfony\Component\Security\Core\Authorization\Voter\RoleHierarchyVoter;

use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\VisitorInterface;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Metadata\StaticPropertyMetadata;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use stdClass;

class CostAndRevenueHandler implements SubscribingHandlerInterface
{
    use \Symfony\Component\DependencyInjection\ContainerAwareTrait;

    public static function getSubscribingMethods()
    {
        $methods = array();
        $formats = array('json', 'xml', 'yml');

        foreach ($formats as $format) {
            $methods[] = array(
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'type' => TravelerId::class,
                'format' => $format,
                'method' => 'serializeTravelerId',
            );
            $methods[] = array(
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'type' => SalesItem::class,
                'format' => $format,
                'method' => 'serializeSalesItem',
            );

            $methods[] = array(
                'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                'type' => TravelerId::class,
                'format' => $format,
                'method' => 'deserializeTravelerId',
            );
            $methods[] = array(
                'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                'type' => SalesItem::class,
                'format' => $format,
                'method' => 'deserializeSalesItem',
            );
        }

        return $methods;
    }

    public function serializeTravelerId(VisitorInterface $visitor, TravelerId $travelerId, array $type, Context $context)
    {
        $classMetadata = $context->getMetadataFactory()->getMetadataForClass(TravelerId::class);
        $visitor->startVisitingObject($classMetadata, $travelerId, ['name' => TravelerId::class], $context);
        $reflObj = new \ReflectionClass($travelerId);
        $props = $reflObj->getProperties();
        foreach($props as $reflProp){
            if($reflProp->getName() === 'cost' and !$this->isGranted('ROLE_ADMIN')){
                continue;
            }
            $reflProp->setAccessible(true);
            $value =  $reflProp->getValue($travelerId);
            $metadata = new StaticPropertyMetadata(TravelerId::class, $reflProp->getName(), $value);
            $visitor->visitProperty($metadata, $value, $context);
        }
        return $visitor->endVisitingObject($classMetadata, $travelerId, ['name' => TravelerId::class], $context);
    }

    public function serializeSalesItem(VisitorInterface $visitor, SalesItem $salesItem, array $type, Context $context)
    {
        $classMetadata = $context->getMetadataFactory()->getMetadataForClass(SalesItem::class);
        $visitor->startVisitingObject($classMetadata, $salesItem, ['name' => SalesItem::class], $context);
        $reflObj = new \ReflectionClass($salesItem);
        $props = $reflObj->getProperties();
        foreach($props as $reflProp){
            if($reflProp->getName() === 'revenue' and !$this->isGranted('ROLE_ADMIN')){
                continue;
            }
            $reflProp->setAccessible(true);
            $value =  $reflProp->getValue($salesItem);
            $metadata = new StaticPropertyMetadata(SalesItem::class, $reflProp->getName(), $value);
            $visitor->visitProperty($metadata, $value, $context);
        }
        return $visitor->endVisitingObject($classMetadata, $salesItem, ['name' => SalesItem::class], $context);
    }

    public function deserializeTravelerId(VisitorInterface $visitor, $data, array $type, Context $context)
    {
        $classMetadata = $context->getMetadataFactory()->getMetadataForClass(TravelerId::class);
        $travelerId = $this->container->get('jms_serializer.object_constructor')
            ->construct($visitor, $classMetadata, $data, $type, $context);
        $visitor->startVisitingObject($classMetadata, $travelerId, ['name' => TravelerId::class], $context);
        foreach($data as $name => $value){
            if($name === 'cost' and !$this->isGranted('ROLE_ADMIN')){
                continue;
            }
            if(array_key_exists($name, $classMetadata->propertyMetadata)){
                $visitor->visitProperty($classMetadata->propertyMetadata[$name], $data, $context);
            }
        }
        return $visitor->endVisitingObject($classMetadata, $travelerId, ['name' => TravelerId::class], $context);
    }

    public function deserializeSalesItem(VisitorInterface $visitor, $data, array $type, Context $context)
    {
        $classMetadata = $context->getMetadataFactory()->getMetadataForClass(SalesItem::class);
        $salesItem = $this->container->get('jms_serializer.object_constructor')
            ->construct($visitor, $classMetadata, $data, $type, $context);
        $visitor->startVisitingObject($classMetadata, $salesItem, ['name' => SalesItem::class], $context);
        foreach($data as $name => $value){
            if($name === 'revenue' and !$this->isGranted('ROLE_ADMIN')){
                continue;
            }
            if(array_key_exists($name, $classMetadata->propertyMetadata)){
                $visitor->visitProperty($classMetadata->propertyMetadata[$name], $data, $context);
            }
        }
        return $visitor->endVisitingObject($classMetadata, $salesItem, ['name' => SalesItem::class], $context);
    }

    private function isGranted($role)
    {
        $roleHierarchyVoter = new RoleHierarchyVoter($this->container->get('security.role_hierarchy'));

        $accessDecisionManager = new AccessDecisionManager(
            [$roleHierarchyVoter],
            'unanimous',
            false,
            false
        );

        $authorizationChecker = new AuthorizationChecker(
            $this->container->get('security.token_storage'),
            $this->container->get('security.authentication.manager'),
            $accessDecisionManager
        );

        return $authorizationChecker->isGranted($role);
    }
}