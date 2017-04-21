<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation As JMS;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table()
 */
Class Unit
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
	 * @ORM\Column(type="string", length=64)
     * @JMS\Type("string")
     */
	protected $serial = null;

	public function getSerial()
	{
		return $this->serial;
	}

	public function setSerial($serial)
	{
		$this->serial = $serial;
		return $this;
	}

	public function generateSerial()
	{
		$serial = Uuid::uuid4();
		$this->setSerial($serial);
		return $serial;
	}



	/**
	 * @ORM\Column(type="text", nullable=true)
     * @JMS\Type("string")
     */
	protected $description = null;

	public function getDescription()
	{
		return $this->description;
	}

	public function setDescription($description)
	{
		$this->description = $description;
		return $this;
	}

	/**
	 * @ORM\OneToOne(targetEntity="TravelerId", inversedBy="unit")
	 * @ORM\JoinColumn(nullable=true, unique=true)
	 * @JMS\Type("AppBundle\Entity\TravelerId")
	 */
	protected $travelerId = null;

	public function getTravelerId()
	{
		return $this->travelerId;
	}

	public function setTravelerId(TravelerId $travelerId = null)
	{
		$this->travelerId = $travelerId;
		return $this;
	}

	/**
	 * @ORM\OneToOne(targetEntity="SalesItem", inversedBy="unit")
	 * @ORM\JoinColumn(nullable=true, unique=true)
	 * @JMS\Type("AppBundle\Entity\SalesItem")
	 */
	protected $salesItem = null;

	public function getSalesItem()
	{
		return $this->salesItem;
	}

	public function setSalesItem(SalesItem $salesItem = null)
	{
		$this->salesItem = $salesItem;
		return $this;
	}

	/**
	 * @ORM\ManyToOne(targetEntity="UnitType")
	 * @ORM\JoinColumn(nullable=false)
	 * @JMS\Type("AppBundle\Entity\UnitType")
	 */
	protected $unitType = null;

	public function getUnitType()
	{
		return $this->unitType;
	}

	public function setUnitType(UnitType $unitType)
	{
		$this->unitType = $unitType;
		return $this;
	}

	/**
	 * @ORM\ManyToOne(targetEntity="Organization")
	 * @ORM\JoinColumn(nullable=false)
	 * @JMS\Exclude
	 */
	protected $organization = null;

	public function getOrganization()
	{
		return $this->organization;
	}

	public function setOrganization(Organization $organization)
	{
		$this->organization = $organization;
		return $this;
	}

	/**
	 * @ORM\Column(type="boolean")
     * @JMS\Type("boolean")
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
     * @ORM\PrePersist
     */
    public function onCreate()
    {
    	if($this->getSerial() === null){
    		$this->generateSerial();
    	}
    	if($this->getIsVoid() === null){
    		$this->setIsVoid(false);
    	}
    }

	public function isOwnedByOrganization(Organization $organization)
    {
        return (
        	$this->getOrganization() === $organization
    	);
    }

}