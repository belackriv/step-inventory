<?php

namespace AppBundle\Controller\Mixin;

use AppBundle\Entity\SalesItem;
use AppBundle\Entity\SalesItemDataTransferObject;
use AppBundle\Entity\InventorySalesItemEdit;
use AppBundle\Entity\InventorySalesItemMovement;

trait SalesItemLogMixin
{
    public function checkForSalesItemEdit(SalesItem $salesItem, SalesItemDataTransferObject $dto)
    {
        $edit = null;
        $oldAttributes = [];
        $newAttributes = [];

        $reflectedObject = new \ReflectionObject($salesItem);
        $reflectedProperties = $reflectedObject->getProperties();

        foreach($reflectedProperties as $reflectedProperty){
            $propertyName = $reflectedProperty->getName();
            if($propertyName !== 'bin' and property_exists($dto, $propertyName)){
                $reflectedProperty->setAccessible(true);
                $liveValue = $reflectedProperty->getValue($salesItem);
                $value = $dto->$propertyName;
                if( $liveValue !== $value ){
                    $oldAttributes[$propertyName] = (is_object($liveValue) and method_exists($liveValue, 'getId'))?$liveValue->getId():$liveValue;
                    $newAttributes[$propertyName] = (is_object($value) and method_exists($value, 'getId'))?$value->getId():$value;
                }
            }
        }

        if(count($oldAttributes) > 0){
            $edit = new InventorySalesItemEdit();
            $edit->setSalesItem($salesItem);
            $edit->setByUser($this->getUser());
            $edit->setEditedAt(new \DateTime());
            $edit->setOldAttributes($oldAttributes);
            $edit->setNewAttributes($newAttributes);
            $this->getDoctrine()->getManager()->persist($edit);
        }
        return $edit;
    }

    public function checkForSalesItemMovement(SalesItem $salesItem, SalesItemDataTransferObject $dto)
    {
        $move = null;
        if($salesItem->getBin() !== $dto->bin){
            $move = new InventorySalesItemMovement();
            $move->setSalesItem($salesItem);
            $move->setByUser($this->getUser());
            $move->setMovedAt(new \DateTime());
            $move->setFromBin($salesItem->getBin());
            $move->setToBin($dto->bin);
            $this->getDoctrine()->getManager()->persist($move);
        }
        return $move;
    }
}