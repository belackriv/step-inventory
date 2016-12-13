<?php

namespace AppBundle\Entity;

use AppBundle\Library\Utilities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation As JMS;


Class MassTravelerId
{
    public function __construct()
    {
        $this->travelerIds = new ArrayCollection();
    }

	/**
     * @JMS\Type("ArrayCollection<AppBundle\Entity\TravelerId>")
     * @JMS\Groups({"Default"})
     */
    protected $travelerIds;

    public function getTravelerIds()
    {
    	return $this->travelerIds;
    }

    public function addTravelerId(TravelerId $travelerId)
    {
        if(!$this->travelerIds->contains($travelerId)){
            $this->travelerIds->add($travelerId);
        }
        return $this;
    }

    public function removeTravelerId(TravelerId $travelerId)
    {
        $this->travelerIds->removeElement($travelerId);
        $this->travelerIds = new ArrayCollection(array_values($this->travelerIds->toArray()));
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

    public function getLogEntityForTravelerId(TravelerId $travelerId, User $user)
    {
        switch ($this->type) {
            case 'edit':
                $logEntity = new InventoryTravelerIdEdit();
                $logEntity->setOldAttributes($this->oldAttributes);
                $logEntity->setNewAttributes($this->newAttributes);
                $logEntity->setEditedAt(new \DateTime());
                $logEntity->setByUser($user);
                $logEntity->setTravelerId($travelerId);
                return $logEntity;
            case 'move':
                $logEntity = new InventoryTravelerIdMovement();
                $logEntity->fromBin($this->fromBin);
                $logEntity->toBin($this->toBin);
                $logEntity->setMovedAt(new \DateTime());
                $logEntity->setByUser($user);
                $logEntity->setTravelerId($travelerId);
                return $logEntity;
            default:
                throw new \Exception("Must Supply a type('edit','move') for a Mass TravelerId Update");
        }
    }

    public function isTransform()
    {
        return ($this->type === 'transform');
    }
}
