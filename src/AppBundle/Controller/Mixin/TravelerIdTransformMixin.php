<?php

namespace AppBundle\Controller\Mixin;

use AppBundle\Entity\TravelerId;
use AppBundle\Entity\SalesItem;
use AppBundle\Entity\InventoryTravelerIdTransform;
use AppBundle\Entity\TransformableEntityInterface;

trait TravelerIdTransformMixin
{
    private $transforms = [];

    public function createTransformEntities(TravelerId $travelerId)
    {
        list($transform, $mergedTravelerId) = $this->getTransform($travelerId);
        $transform->setByUser($this->getUser());
        $transform->setTransformedAt(new \DateTime);
        $this->getDoctrine()->getManager()->persist($transform);

        $transformEntities = [];
        foreach($transform->getToTravelerIds() as $toTravelerId){
            $unit = $toTravelerId->checkUnitStatus();
            if($unit and !in_array($unit, $transformEntities)){
                $transformEntities[] = $unit;
            }
            $toTravelerId->generateLabel();
            $toTravelerId->setReverseTransform($transform);
            if($toTravelerId->getCost() === null){
                $this->setCost($toTravelerId);
            }
            $this->getDoctrine()->getManager()->persist($toTravelerId);
            if(!in_array($toTravelerId, $transformEntities)){
                $transformEntities[] = $toTravelerId;
            }
        }
        foreach($transform->getToSalesItems() as $toSalesItem){
            $unit = $toSalesItem->checkUnitStatus();
            if($unit and !in_array($unit, $transformEntities)){
                $transformEntities[] = $unit;
            }
            $toSalesItem->setReverseTransform($transform);
            if($toSalesItem->getCost() === null){
                $this->setCost($toSalesItem);
            }
            $this->getDoctrine()->getManager()->persist($toSalesItem);
            if(!in_array($toSalesItem, $transformEntities)){
                $transformEntities[] = $toSalesItem;
            }
        }
        return [$transformEntities, $transform, $mergedTravelerId];
    }

    private function getTransform(TravelerId $travelerId)
    {
        $transform = $travelerId->getTransform();

        foreach($this->transforms as $storedTransform){
            if($transform->cid == $storedTransform->cid){
                $transform = $storedTransform;
                break;
            }
        }
        if(!in_array($transform, $this->transforms)){
            $this->transforms[] = $transform;
        }

        $transform->getFromTravelerIds()->removeElement($travelerId);
        $mergedTravelerId = $this->getDoctrine()->getManager()->merge($travelerId);
        $transform->addFromTravelerId($mergedTravelerId);

        return [$transform, $mergedTravelerId];
    }

    private function setCost(TransformableEntityInterface $entity)
    {
        $transform = $entity->getReverseTransform();
        $cost = 0;
        $count = 0;
        foreach($transform->getFromTravelerIds() as $fromTravelerId){
            $cost += $fromTravelerId->getCost();
            $count++;
        }
        if($count != 0){
            $entity->setCost(($cost/$count) * $transform->getRatio());
        }
    }

}