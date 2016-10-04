<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation As JMS;

/**
 * @ORM\Entity
 * @ORM\Table()
 */
Class BinType
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
	 * @ORM\Column(type="text")
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
	 * @ORM\ManyToOne(targetEntity="Organization", inversedBy="binTypes")
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
	 * @ORM\Column(type="simple_array", nullable=true)
	 * @JMS\Type("array")
	 */

	protected $behavoirs = null;

	public function getBehavoirs()
	{
		return $this->behavoirs;
	}

	public function setBehavoirs(array $behavoirs)
	{
		$this->behavoirs = $behavoirs;
		return $this;
	}

	/**
	 * @ORM\Column(type="boolean")
     * @JMS\Type("boolean")
     */
	protected $isActive = null;

	public function getIsActive()
	{
		return $this->isActive;
	}

	public function setIsActive($isActive)
	{
		$this->isActive = $isActive;
		return $this;
	}

	 /**
     * @var array
     * @JMS\ReadOnly
     */
    public static $behavoirList = [
        'cannotHaveParent' => 'Can not have parent',

    ];


    public function canHaveParent()
    {
    	return (in_array($this->restrictions, 'cannotHaveParent'))?false:true;
    }

    public function isOwnedByOrganization(Organization $organization)
    {
        return ($this->getOrganization() === $organization);
    }
}