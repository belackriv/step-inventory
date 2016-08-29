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
        if($travelerId->getInboundOrder() !== $this){
        	$travelerId->setInboundOrder($this);
        }
        return $this;
    }

    public function removeTravelerId(TravelerId $travelerId)
    {
        $this->travelerIds->removeElement($travelerId);
        $travelerId->setInboundOrder(null);
        $this->travelerIds = new ArrayCollection(array_values($this->children->toArray()));
    }

}
