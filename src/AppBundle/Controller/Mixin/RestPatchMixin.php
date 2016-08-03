<?php

namespace AppBundle\Controller\Mixin;

use Symfony\Component\HttpFoundation\Request;


trait RestPatchMixin
{
    public function patchEntity($entity, $patch, Request $request)
    {
        $reflectedObject = new \ReflectionObject($patch);
        $reflectedProperties = $reflectedObject->getProperties();
        foreach($reflectedProperties as $property){
            if( $request->request->has($property->getName()) ){
                $property->setAccessible(true);
                $propertyValue = $property->getValue($patch);
                $setMethodName = 'set'.$property->getName();
                $entity->$setMethodName($propertyValue);
            }
        }
    }
}