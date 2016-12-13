<?php

namespace AppBundle\Controller\Mixin;

use AppBundle\Entity\TravelerId;
use AppBundle\Entity\SalesItem;
use AppBundle\Entity\InventoryTravelerIdTransform;

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
            $toTravelerId->generateLabel();
            $toTravelerId->setReverseTransform($transform);
            $this->getDoctrine()->getManager()->persist($toTravelerId);
            if(!in_array($toTravelerId, $transformEntities)){
                $transformEntities[] = $toTravelerId;
            }
        }
        foreach($transform->getToSalesItems() as $toSalesItem){
            $toSalesItem->setReverseTransform($transform);
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

}