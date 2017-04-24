<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation As JMS;

/**
 * @ORM\Entity
 * @ORM\Table()
 */
Class UnitProperty
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
	 * @ORM\Column(type="integer", nullable=true)
     * @JMS\Type("integer")
     */
	protected $integerValue = null;

	public function getIntegerValue()
	{
		return $this->integerValue;
	}

	public function setIntegerValue($integerValue)
	{
		$this->integerValue = $integerValue;
		return $this;
	}

	/**
	 * @ORM\Column(type="decimal", precision=7, scale=2, nullable=true)
     * @JMS\Type("float")
     */
	protected $floatValue = null;

	public function getFloatValue()
	{
		return $this->floatValue;
	}

	public function setFloatValue($floatValue)
	{
		$this->floatValue = $floatValue;
		return $this;
	}

	/**
	 * @ORM\Column(type="boolean", nullable=true)
     * @JMS\Type("boolean")
     */
	protected $booleanValue = null;

	public function getBooleanValue()
	{
		return $this->booleanValue;
	}

	public function setBooleanValue($booleanValue)
	{
		$this->booleanValue = $booleanValue;
		return $this;
	}

	/**
	 * @ORM\Column(type="string", length=256, nullable=true)
     * @JMS\Type("string")
     */
	protected $stringValue = null;

	public function getStringValue()
	{
		return $this->stringValue;
	}

	public function setStringValue($stringValue)
	{
		$this->stringValue = $stringValue;
		return $this;
	}

	/**
	 * @ORM\ManyToOne(targetEntity="Unit", inversedBy="properties")
	 * @ORM\JoinColumn(nullable=false)
	 * @JMS\Type("AppBundle\Entity\Unit")
	 */
	protected $unit = null;

	public function getUnit()
	{
		return $this->unit;
	}

	public function setUnit(Unit $unit)
	{
		$this->unit = $unit;
		return $this;
	}

	/**
	 * @ORM\ManyToOne(targetEntity="UnitTypeProperty")
	 * @ORM\JoinColumn(nullable=false)
	 * @JMS\Type("AppBundle\Entity\UnitTypeProperty")
	 */
	protected $unitTypeProperty = null;

	public function getUnitTypeProperty()
	{
		return $this->unitTypeProperty;
	}

	public function setUnitTypeProperty(UnitTypeProperty $unitTypeProperty)
	{
		$this->unitTypeProperty = $unitTypeProperty;
		return $this;
	}

	public function isOwnedByOrganization(Organization $organization)
    {
        return ($this->getUnit()->isOwnedByOrganization($organization));
    }



}