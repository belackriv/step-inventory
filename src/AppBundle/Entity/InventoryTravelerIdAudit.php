<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation As JMS;

/**
 * @ORM\Entity
 * @ORM\Table()
 */
Class InventoryTravelerIdAudit
{

	/**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Type("integer")
     */
	protected $id = null;

	public function getId()
	{
		return $this->id;
	}

	/**
	 * @ORM\ManyToOne(targetEntity="InventoryAudit", inversedBy="inventoryTravelerIdAudits")
	 * @JMS\Type("AppBundle\Entity\InventoryAudit")
	 */

	protected $inventoryAudit = null;

	public function getInventoryAudit()
	{
		return $this->inventoryAudit;
	}

	public function setInventoryAudit(InventoryAudit $inventoryAudit)
	{
		if($inventoryAudit === null){
			$this->inventoryAudit = null;
			$inventoryAudit->removeInventoryTravelerIdAudit($this);
		}else{
			$this->inventoryAudit = $inventoryAudit;
			$inventoryAudit->addInventoryTravelerIdAudit($this);
		}
		return $this;
	}

	/**
	 * @ORM\Column(type="string", length=64, unique=true)
     * @JMS\Type("string")
     */
	protected $travelerIdLabel = null;

	public function getTravelerIdLabel()
	{
		return $this->travelerIdLabel;
	}

	public function setTravelerIdLabel($travelerIdLabel)
	{
		$this->travelerIdLabel = $travelerIdLabel;
		return $this;
	}

	/**
	 * @ORM\ManyToOne(targetEntity="TravelerId")
	 * @ORM\JoinColumn(nullable=false)
	 * @JMS\Type("AppBundle\Entity\TravelerId")
	 */

	protected $travelerId = null;

	public function getTravelerId()
	{
		return $this->travelerId;
	}

	public function setTravelerId(TravelerId $travelerId)
	{
		$this->travelerId = $travelerId;
		return $this;
	}


	public function isValid(User $user)
	{
		if($this->getTravelerIdLabel() === null){
            throw new \Exception('TravelerId Label Must Be Set.');
        }
        if($this->getTravelerId() === null){
            throw new \Exception('No TravelerId Found.');
        }
        foreach($this->inventoryAudit->getInventoryTravelerIdAudits() as $inventoryTravelerIdAudit){
        	if($inventoryTravelerIdAudit !== $this and $inventoryTravelerIdAudit->getTravelerId() === $this->getTravelerId()){
        		throw new \Exception('This TravelerId already has been included in this audit.');
        	}
        }
        if($this->inventoryAudit->getByUser() !== $user){
        	throw new \Exception('Count update an audit not started by yourself.');
        }
         if($this->inventoryAudit->getEndedAt()){
        	throw new \Exception('Count update an audit that has ended.');
        }
        return true;
	}

	public function isOwnedByOrganization(Organization $organization)
	{
		return (
			$this->getInventoryAudit() and $this->getInventoryAudit()->isOwnedByOrganization($organization)
		);
	}
}