<?php

namespace AppBundle\Entity;

use AppBundle\Library\Utilities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation As JMS;


Class MassSalesItem
{
    public function __construct()
    {
        $this->salesItems = new ArrayCollection();
    }

	/**
     * @JMS\Type("ArrayCollection<AppBundle\Entity\SalesItem>")
     * @JMS\Groups({"Default"})
     */
    protected $salesItems;

    public function getSalesItems()
    {
    	return $this->salesItems;
    }

    public function addSalesItem(SalesItem $salesItem)
    {
        if(!$this->salesItems->contains($salesItem)){
            $this->salesItems->add($salesItem);
        }
        return $this;
    }

    public function removeSalesItem(SalesItem $salesItem)
    {
        $this->salesItems->removeElement($salesItem);
        $this->salesItems = new ArrayCollection(array_values($this->children->toArray()));
    }

    /**
     * @JMS\Type("string")
     */
    public $type;

    /**
     * @JMS\Type("array")
     */
    public $oldAttributes;

    /**
     * @JMS\Type("array")
     */
    public $newAttributes;

    /**
     * @JMS\Type("AppBundle\Entity\Bin")
     */
    public $fromBin;

    /**
     * @JMS\Type("AppBundle\Entity\Bin")
     */
    public $toBin;

    public function getLogEntityForSalesItem(SalesItem $salesItem, User $user)
    {
        switch ($this->type) {
            case 'edit':
                $logEntity = new InventorySalesItemEdit();
                $logEntity->setOldAttributes($this->oldAttributes);
                $logEntity->setNewAttributes($this->newAttributes);
                $logEntity->setEditedAt(new \DateTime());
                $logEntity->setByUser($user);
                $logEntity->setSalesItem($salesItem);
                return $logEntity;
            case 'move':
                $logEntity = new InventorySalesItemMovement();
                $logEntity->fromBin($this->fromBin);
                $logEntity->toBin($this->toBin);
                $logEntity->setMovedAt(new \DateTime());
                $logEntity->setByUser($user);
                $logEntity->setSalesItem($salesItem);
                return $logEntity;
            default:
                throw new \Exception("Must Supply a type('edit','move') for a Mass Sales Item Update");
        }
    }
}
