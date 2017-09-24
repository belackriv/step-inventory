<?php

namespace AppBundle\Entity;

use AppBundle\Library\Utilities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation As JMS;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table()
 */
Class TravelerId implements TransformableEntityInterface
{

	/**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Type("integer")
     * @JMS\Groups({"Default","OrderManifest"})
     */
	protected $id = null;

	public function getId()
	{
		return $this->id;
	}

	/**
	 * @ORM\Column(type="datetime", nullable=false)
	 * @JMS\Type("DateTime")
	 */

	protected $createdAt = null;

	public function getCreatedAt()
	{
		return $this->createdAt;
	}

	public function setCreatedAt(\DateTime $createdAt)
	{
		$this->createdAt = $createdAt;
		return $this;
	}

	/**
	 * @ORM\ManyToOne(targetEntity="InboundOrder", inversedBy="travelerIds")
	 * @ORM\JoinColumn(nullable=false)
	 * @JMS\Type("AppBundle\Entity\InboundOrder")
	 */
	protected $inboundOrder = null;

	public function getInboundOrder()
	{
		return $this->inboundOrder;
	}

	public function setInboundOrder(InboundOrder $inboundOrder)
	{
		$this->inboundOrder = $inboundOrder;
		$this->inboundOrder->addTravelerId($this);
		$this->generateLabel();
		return $this;
	}

	/**
	 * @ORM\Column(type="string", length=64)
     * @JMS\Type("string")
     * @JMS\Groups({"Default","OrderManifest"})
     */
	protected $label = null;

	public function getLabel()
	{
		return $this->label;
	}

	public function setLabel($label)
	{
		$this->label = $label;
		return $this;
	}

	public function generateLabel()
	{
		$tids = $this->getInboundOrder()->getTravelerIds();
		if(is_a($tids, 'Doctrine\ORM\PersistentCollection')){
			$tids->initialize();
		}
		$this->getInboundOrder()->addTravelerId($this);
		$label = $this->getInboundOrder()->getLabel().'-'.
			Utilities::baseEncode($tids->indexOf($this)+1);
		$this->setLabel($label);
		return $label;
	}

	/**
	 * @ORM\ManyToOne(targetEntity="Bin", inversedBy="travelerIds")
	 * @ORM\JoinColumn(nullable=false)
	 * @JMS\Type("AppBundle\Entity\Bin")
	 * @JMS\Groups({"Default","OrderManifest"})
	 */
	protected $bin = null;

	public function getBin()
	{
		return $this->bin;
	}

	public function setBin(Bin $bin)
	{
		$this->bin = $bin;
		return $this;
	}

	/**
	 * @ORM\ManyToOne(targetEntity="Sku", )
	 * @ORM\JoinColumn(nullable=false)
	 * @JMS\Type("AppBundle\Entity\Sku")
	 * @JMS\Groups({"Default","OrderManifest"})
	 */
	protected $sku = null;

	public function getSku()
	{
		return $this->sku;
	}

	public function setSku(Sku $sku)
	{
		$this->sku = $sku;
		return $this;
	}

	/**
	 * @ORM\OneToOne(targetEntity="Unit", mappedBy="travelerId", cascade={"persist"})
	 * @JMS\Type("AppBundle\Entity\Unit")
	 * @JMS\Groups({"Default","OrderManifest"})
	 */
	protected $unit = null;

	public function getUnit()
	{
		return $this->unit;
	}

	public function setUnit(Unit $unit = null)
	{
		$this->unit = $unit;
		return $this;
	}

	/**
	 * @ORM\Column(type="boolean")
     * @JMS\Type("boolean")
     * @JMS\Groups({"Default","OrderManifest"})
     */
	protected $isVoid = null;

	public function getIsVoid()
	{
		return $this->isVoid;
	}

	public function setIsVoid($isVoid)
	{
		$this->isVoid = $isVoid;
		return $this;
	}

	/**
	 * @ORM\Column(type="decimal", precision=7, scale=2, nullable=false)
	 * @JMS\Type("float")
	 * @JMS\Groups({"Default","OrderManifest"})
	 */
	protected $quantity;

	public function getQuantity()
	{
		return $this->quantity;
	}

	public function setQuantity($quantity)
	{
		$this->quantity = $quantity;
		return $this;
	}

	/**
	 * @ORM\Column(type="decimal", precision=7, scale=2, nullable=true)
	 * @JMS\Type("float")
	 * @JMS\Groups({"Default","OrderManifest"})
	 */
	protected $cost;

	public function getCost()
	{
		return $this->cost;
	}

	public function setCost($cost)
	{
		$this->cost = $cost;
		return $this;
	}

	/**
	 * @ORM\ManyToOne(targetEntity="InventoryTravelerIdTransform", inversedBy="fromTravelerIds")
	 * @ORM\JoinColumn(nullable=true)
	 * @JMS\Type("AppBundle\Entity\InventoryTravelerIdTransform")
	 * @JMS\Groups({"Default","OrderManifest"})
	 */
	protected $transform = null;

	public function getTransform()
	{
		return $this->transform;
	}

	public function setTransform(InventoryTravelerIdTransform $transform = null)
	{
		if($transform and !$transform->getFromTravelerIds()->contains($this)){
			$this->transform = $transform;
			$transform->addFromTravelerId($this);
		}else if(	$transform === null and $this->transform and
					$this->transform->getFromTravelerIds()->contains($this)){
			$this->transform->removeFromTravelerId($this);
			$this->transform = $transform;
		}else{
			$this->transform = $transform;
		}
		return $this;
	}

	/**
	 * @ORM\ManyToOne(targetEntity="InventoryTravelerIdTransform", inversedBy="toTravelerIds")
	 * @ORM\JoinColumn(nullable=true)
	 * @JMS\Type("AppBundle\Entity\InventoryTravelerIdTransform")
	 */
	protected $reverseTransform = null;

	public function getReverseTransform()
	{
		return $this->reverseTransform;
	}

	public function setReverseTransform(InventoryTravelerIdTransform $transform = null)
	{
		if($transform and !$transform->getToTravelerIds()->contains($this)){
			$this->reverseTransform = $transform;
			$transform->addToTravelerId($this);
		}else if(	$transform === null and $this->reverseTransform and
					$this->reverseTransform->getToTravelerIds()->contains($this)){
			$this->reverseTransform->removeToTravelerId($this);
			$this->reverseTransform = $transform;
		}else{
			$this->reverseTransform = $transform;
		}
		return $this;
	}

	/**
     * @ORM\PrePersist
     */
    public function onCreate()
    {
    	if($this->getIsVoid() === null){
    		$this->setIsVoid(false);
    	}
    	$this->setCreatedAt(new \DateTime());
    }

    public function isOwnedByOrganization(Organization $organization)
    {
        return (
        	$this->getInboundOrder() and $this->getInboundOrder()->isOwnedByOrganization($organization) and
			$this->getBin() and $this->getBin()->isOwnedByOrganization($organization) and
			$this->getSku() and $this->getSku()->isOwnedByOrganization($organization)
		);
    }


    public function __toString()
    {
    	return (string)$this->label;
    }

}
