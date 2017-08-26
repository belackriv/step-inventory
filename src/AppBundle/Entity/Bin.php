<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation As JMS;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\BinRepository")
 * @ORM\Table()
 */
Class Bin
{
	public function __construct()
    {
        $this->partCounts = new ArrayCollection();
        $this->travelerIds = new ArrayCollection();
        $this->salesItems = new ArrayCollection();
    }

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
	 * @ORM\Column(type="string", length=64)
     * @JMS\Type("string")
     * @JMS\Groups({"Default","OrderManifest"})
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
	 * @ORM\ManyToOne(targetEntity="Department", inversedBy="bins")
	 * @ORM\JoinColumn(nullable=false)
	 * @JMS\Type("AppBundle\Entity\Department")
	 */

	protected $department = null;

	public function getDepartment()
	{
		return $this->department;
	}

	public function setDepartment(Department $department)
	{
		$this->department = $department;
		return $this;
	}

	/**
	 * @ORM\ManyToOne(targetEntity="PartCategory")
	 * @JMS\Type("AppBundle\Entity\PartCategory")
	 */

	protected $partCategory = null;

	public function getPartCategory()
	{
		return $this->partCategory;
	}

	public function setPartCategory(PartCategory $partCategory)
	{
		$this->partCategory = $partCategory;
		return $this;
	}

	//will add DeviceType and ComodityType



	/**
	 * @ORM\ManyToOne(targetEntity="BinType", )
	 * @ORM\JoinColumn(nullable=false)
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
     * @ORM\OneToMany(targetEntity="Bin", mappedBy="parent")
     * @JMS\ReadOnly
     */
    protected $children;

    public function getChildren()
    {
    	return $this->children;
    }

    public function setChildren(ArrayCollection $children)
    {
    	$this->children = $children;

    	return $this;
    }

    /**
     * Add child
     *
     * @param \AppBundle\Entity\Bin $child
     * @return Bin
     */
    public function addChild(Bin $child)
    {
        $this->children[] = $child;
        $child->setParent($this);
        return $this;
    }

    /**
     * Remove child
     *
     * @param \AppBundle\Entity\Bin $child
     */
    public function removeChild(Bin $child)
    {
        $this->children->removeElement($child);
        $child->setParent(null);
        $this->children = new ArrayCollection(array_values($this->children->toArray()));
    }

	/**
	 * @ORM\ManyToOne(targetEntity="Bin", inversedBy="children", cascade={"all"})
     * @ORM\JoinColumn(onDelete="SET NULL")
	 * @JMS\Type("AppBundle\Entity\Bin")
	 */

	protected $parent = null;

	public function getParent()
	{
		return $this->parent;
	}

	public function setParent(Bin $bin)
	{
		$this->parent = $bin;
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
		$this->isActive = (boolean)$isActive;
		return $this;
	}

	/**
	 * @ORM\Column(type="boolean")
     * @JMS\Type("boolean")
     */
	protected $isLocked = null;

	public function getIsLocked()
	{
		return $this->isLocked;
	}

	public function isLocked()
	{
		return $this->isLocked;
	}

	public function setIsLocked($isLocked)
	{
		$this->isLocked = (boolean)$isLocked;
		return $this;
	}

	/**
     * @ORM\OneToMany(targetEntity="BinSkuCount", mappedBy="bin")
     * @JMS\Type("ArrayCollection<AppBundle\Entity\BinSkuCount>")
     * @JMS\Groups({"BinSkuCount"})
     * @JMS\ReadOnly
     */
    protected $skuCounts;

    public function getSkuCounts()
    {
    	return $this->skuCounts;
    }

    /**
     * @ORM\OneToMany(targetEntity="TravelerId", mappedBy="bin")
     * @JMS\Type("ArrayCollection<AppBundle\Entity\TravelerId>")
     * @JMS\Groups({"TravelerId","Bin"})
     * @JMS\ReadOnly
     */
    protected $travelerIds;

    public function getTravelerIds()
    {
    	return $this->travelerIds;
    }

    /**
     * @ORM\OneToMany(targetEntity="SalesItem", mappedBy="bin")
     * @JMS\Type("ArrayCollection<AppBundle\Entity\SalesItem>")
     * @JMS\Groups({"SalesItem","Bin"})
     * @JMS\ReadOnly
     */
    protected $salesItems;

    public function getSalesItems()
    {
    	return $this->salesItems;
    }

    public function isOwnedByOrganization(Organization $organization)
	{
		if(!$this->getBinType()){
			throw new \Exception("Bins must Have a Bin Type");
		}
		if(!$this->getDepartment()){
			throw new \Exception("Bins must Have a Department");
		}
		return (
			$this->getBinType() and $this->getBinType()->isOwnedByOrganization($organization) and
			$this->getDepartment() and $this->getDepartment()->isOwnedByOrganization($organization) and
			(!$this->getPartCategory() or $this->getPartCategory()->isOwnedByOrganization($organization) ) and
			(!$this->getParent() or $this->getParent()->isOwnedByOrganization($organization) )
		);
	}

	public function getSelectOptionData()
	{
		return [
			'id' => $this->id,
			'name' => $this->name,
			'isActive' => $this->isActive
		];
	}
}