<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation As JMS;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table()
 */
Class InventoryMovementRule
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
	 * @ORM\Column(type="string", length=64, nullable=false)
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
	 * @ORM\ManyToOne(targetEntity="Role")
	 * @JMS\Type("AppBundle\Entity\Role")
	 */

	protected $role = null;

	public function getRole()
	{
		return $this->role;
	}

	public function setRole(Role $role)
	{
		$this->role = $role;
		return $this;
	}

	/**
	 * @ORM\ManyToOne(targetEntity="BinType")
	 * @JMS\Type("AppBundle\Entity\BinType")
	 */

	protected $binType = null;

	public function getBinType()
	{
		return $this->binType;
	}

	public function setBinType(BinType $binType)
	{
		$this->binType = $binType;
		return $this;
	}

	/**
	 * @ORM\Column(type="simple_array", nullable=true)
	 * @JMS\Type("array")
	 */

	protected $restrictions = null;

	public function getRestrictions()
	{
		return $this->restrictions;
	}

	public function setRestrictions(array $restrictions)
	{
		$this->restrictions = $restrictions;
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
     * @ORM\PrePersist
     */
    public function onCreate()
    {
    	if(!$this->isActive){
    		$this->isActive = false;
    	}
    }

    public function isOwnedByOrganization(Organization $organization)
	{
		return ( $this->getBinType()->isOwnedByOrganization($organization) );
	}
}