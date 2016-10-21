<?php

namespace AppBundle\Entity;

use AppBundle\Library\Utilities;

use Ramsey\Uuid\Uuid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation As JMS;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(uniqueConstraints={
 *		@ORM\UniqueConstraint(name="org_sku_number_unique", columns={"organization_id", "number"}),
 *		@ORM\UniqueConstraint(name="org_sku_label_unique", columns={"organization_id", "label"})
 *	})
 */
Class Sku
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
	protected $name = null;

	public function getName()
	{
		return $this->name;
	}

	public function setName($name)
	{
		$this->name = $name;
		return $this;
	}

	/**
	 * @ORM\Column(type="integer", length=64)
     * @JMS\Type("integer")
     */
	protected $number = null;

	public function getNumber()
	{
		return $this->number;
	}

	public function setNumber($number)
	{
		$this->number = $number;
		return $this;
	}

	/**
	 * @ORM\Column(type="string", length=64)
     * @JMS\Type("string")
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

	/**
	 * @ORM\ManyToOne(targetEntity="Part")
	 * @ORM\JoinColumn(nullable=true)
	 * @JMS\Type("AppBundle\Entity\Part")
	 */

	protected $part = null;

	public function getPart()
	{
		return $this->part;
	}

	public function setPart(Part $part)
	{
		$this->part = $part;
		return $this;
	}

	/**
	 * @ORM\ManyToOne(targetEntity="Commodity")
	 * @ORM\JoinColumn(nullable=true)
	 * @JMS\Type("AppBundle\Entity\Commodity")
	 */

	protected $commodity = null;

	public function getCommodity()
	{
		return $this->commodity;
	}

	public function setCommodity(Commodity $commodity)
	{
		$this->commodity = $commodity;
		return $this;
	}

	/**
	 * @ORM\ManyToOne(targetEntity="UnitType")
	 * @ORM\JoinColumn(nullable=true)
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
	 * @ORM\Column(type="decimal", precision=7, scale=2, nullable=false)
	 * @JMS\Type("string")
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
	 * @JMS\Type("string")
	 */
	protected $averageValue;

	public function getAverageValue()
	{
		return $this->averageValue;
	}

	public function setAverageValue()
	{
		$this->averageValue = $averageValue;
		return $this;
	}

	public function calculateNewAverageValue($value, $count)
	{
		//do this is a service?


		return $this;
	}

	/**
	 * @ORM\ManyToOne(targetEntity="Organization", inversedBy="skus")
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
     * @ORM\PrePersist
     */
    public function onCreate()
    {
    	if($this->getQuantity() === null){
    		$this->setQuantity(0);
    	}
    	if($this->getIsVoid() === null){
    		$this->setIsVoid(false);
    	}
    }

    /**
     * @ORM\PreUpdate
     */
    public function onUpdate()
    {
    	$items = [
    		$this->getPart(),
    		$this->getCommodity(),
    		$this->getUnitType()
    	];
    	$hasItem = false;
		foreach($items as $item){
			if($item !== null){
				if($hasItem === true){
					throw new \Exception("Sku can only have one of part, commodity, or unit.");
				}
				$hasItem = true;
			}
		}
		if($hasItem === false){
			throw new \Exception("Sku must have one of part, commodity, or unit.");
		}
    }

    public function isOwnedByOrganization(Organization $organization)
    {
        return ( $this->getOrganization() === $organization );
    }

    public function getSelectOptionData()
	{
		return [
			'id' => $this->id,
			'name' => $this->name,
			'label' => $this->label,
			'isVoid' => $this->isVoid
		];
	}
}
