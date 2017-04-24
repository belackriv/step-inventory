<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation As JMS;

/**
 * @ORM\Entity
 * @ORM\Table()
 */
Class UnitTypeProperty
{

	const TYPE_INTEGER = 'integer';
	const TYPE_FLOAT = 'float';
	const TYPE_BOOLEAN = 'boolean';
	const TYPE_STRING = 'string';

	/**
	 * @JMS\Exclude
	 */
	public static $types = [
		self::TYPE_INTEGER => 'Integer',
		self::TYPE_STRING => 'String',
		self::TYPE_BOOLEAN => 'Boolean',
		self::TYPE_FLOAT => 'Float',
	];

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
	protected $propertyName = null;

	public function getPropertyName()
	{
		return $this->propertyName;
	}

	public function setPropertyName($propertyName)
	{
		$this->propertyName = $propertyName;
		return $this;
	}

	/**
	 * @ORM\Column(type="string", length=64)
     * @JMS\Type("string")
     */
	protected $propertyType = null;

	public function getPropertyType()
	{
		return $this->propertyType;
	}

	public function setPropertyType($propertyType)
	{
		if(!in_array($propertyType, array_values(self::$types))){
			throw new \Exception("Type $propertyType not supported");

		}
		$this->propertyType = $propertyType;
		return $this;
	}

	/**
	 * @ORM\Column(type="boolean")
     * @JMS\Type("boolean")
     */
	protected $isRequired = null;

	public function getIsRequired()
	{
		return $this->isRequired;
	}

	public function setIsRequired($isRequired)
	{
		$this->isRequired = $isRequired;
		return $this;
	}

	/**
	 * @ORM\ManyToOne(targetEntity="UnitType", inversedBy="properties")
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
	 * @ORM\OneToMany(targetEntity="UnitTypePropertyValidValue", mappedBy="unitTypeProperty", cascade={"persist", "remove"}, orphanRemoval=true)
	 * @JMS\Type("ArrayCollection<AppBundle\Entity\UnitTypePropertyValidValue>")
	 */
	protected $validValues = null;

	public function getValidValues()
	{
		return $this->validValues;
	}

	public function addValidValue(UnitTypePropertyValidValue $value)
    {
        if(!$this->validValues->contains($value)){
            $this->validValues->add($value);
        }
        if($value->getUnitTypeProperty() !== $this){
        	$value->setUnitTypeProperty($this);
        }
        return $this;
    }

    public function removeValidValue(UnitTypePropertyValidValue $value)
    {
        $this->validValues->removeElement($value);
        $this->validValues = new ArrayCollection(array_values($this->validValues->toArray()));
        return $this;
    }

	public function isOwnedByOrganization(Organization $organization)
    {
        return ($this->getUnitType()->getOrganization() === $organization);
    }



}