<?php

namespace AppBundle\Controller\Mixin;

use AppBundle\Entity\TravelerId;
use AppBundle\Entity\InventoryTravelerIdEdit;
use AppBundle\Entity\InventoryTravelerIdMovement;

trait TravelerIdLogMixin
{
    public function checkForTravelerIdEdit(TravelerId $liveTravelerId, TravelerId $travelerId)
    {
        $edit = null;
        $oldAttributes = [];
        $newAttributes = [];

        $reflectedObject = new \ReflectionObject($travelerId);
        $reflectedProperties = $reflectedObject->getProperties();

        foreach($reflectedProperties as $reflectedProperty){
            $propertyName = $reflectedProperty->getName();
            if($propertyName !== 'bin'){
                $reflectedProperty->setAccessible(true);
                $liveValue = $reflectedProperty->getValue($liveTravelerId);
                $value = $reflectedProperty->getValue($travelerId);
                if( $liveValue !== $value ){
                    $oldAttributes[$propertyName] = (is_object($liveValue) and method_exists($liveValue, 'getId'))?$liveValue->getId():$liveValue;
                    $newAttributes[$propertyName] = (is_object($value) and method_exists($value, 'getId'))?$value->getId():$value;
                }
            }
        }

        if(count($oldAttributes) > 0){
            $edit = new InventoryTravelerIdEdit();
            $edit->setTravelerId($liveTravelerId);
            $edit->setByUser($this->getUser());
            $edit->setEditedAt(new \DateTime());
            $edit->setOldAttributes($oldAttributes);
            $edit->setNewAttributes($newAttributes);
            $this->getDoctrine()->getManager()->persist($edit);
        }
        return $edit;
    }

    public function checkForTravelerIdMovement(TravelerId $liveTravelerId, TravelerId $travelerId)
    {
        $move = null;
        if($liveTravelerId->getBin() !== $travelerId->getBin()){
            if($liveTravelerId->getBin()->isLocked()){
                throw new \Exception('Bin "'.$liveTravelerId->getBin()->getName().'" is locked.');
            }
            if($travelerId->getBin()->isLocked()){
                throw new \Exception('Bin "'.$travelerId->getBin()->getName().'" is locked.');
            }
            $move = new InventoryTravelerIdMovement();
            $move->setTravelerId($liveTravelerId);
            $move->setByUser($this->getUser());
            $move->setMovedAt(new \DateTime());
            $move->setFromBin($liveTravelerId->getBin());
            $move->setToBin($travelerId->getBin());
            $this->getDoctrine()->getManager()->persist($move);
        }
        return $move;
    }
}