<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation As JMS;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table()
 */
Class MenuItem
{

	/**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Type("integer")
     * @JMS\Groups({"Default","MenuItem"})
     */

	protected $id = null;

	public function getId()
	{
		return $this->id;
	}

	/**
	 * @ORM\Column(type="boolean")
     * @JMS\Type("boolean")
     * @JMS\Groups({"Default","MenuItem","GetMyself"})
     */

	protected $isActive = null;

	public function isActive($value = null)
	{
		if(isset($value)){
			$this->isActive = $value;
			return $this;
		}else{
			return $this->isActive;
		}
	}

	public function isEnabled()
    {
        return $this->isActive;
    }


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
     * @ORM\Column(type="integer")
     * @JMS\Type("integer")
     * @JMS\Groups({"Default","MenuItem","GetMyself"})
     */

	protected $position = null;

	public function getPosition()
	{
		return $this->position;
	}

	public function setPosition($position)
	{
		$this->position = $position;
		return $this;
	}

	/**
	 * @ORM\ManyToOne(targetEntity="MenuLink")
	 * @JMS\Type("AppBundle\Entity\MenuLink")
	 * @JMS\Groups({"Default","MenuItem","GetMyself"})
	 */

	protected $menuLink = null;

	public function getMenuLink()
	{
		return $this->menuLink;
	}

	public function setMenuLink($menuLink)
	{
		$this->menuLink = $menuLink;
		return $this;
	}

	/**
	 * @ORM\ManyToOne(targetEntity="Department", inversedBy="menuItems")
	 * @JMS\Type("AppBundle\Entity\Department")
	 * @JMS\Groups({"Default","MenuItem"})
	 */

	protected $department = null;

	public function getDepartment()
	{
		return $this->department;
	}

	public function setDepartment($department)
	{
		if($this->parent !== null){
			throw new \Exception("Cannot have a parent and department.");
		}
		$this->department = $department;
		return $this;
	}


	/**
     * @ORM\OneToMany(targetEntity="MenuItem", mappedBy="parent")
     * @JMS\Type("ArrayCollection<AppBundle\Entity\MenuItem>")
     * @JMS\Groups({"Default","GetMyself"})
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
     * @param \AppBundle\Entity\MenuItem $menuItem
     * @return Department
     */
    public function addChild(MenuItem $child)
    {
        $this->children[] = $child;
        $child->setParent($this);
        return $this;
    }

    /**
     * Remove child
     *
     * @param \AppBundle\Entity\MenuItem $menuItem
     */
    public function removeChild(MenuItem $child)
    {
        $this->children->removeElement($child);
        $child->setParent(null);
        $this->children = new ArrayCollection(array_values($this->children->toArray()));
    }

    /**
     * @ORM\ManyToOne(targetEntity="MenuItem", inversedBy="children", cascade={"all"})
     * @ORM\JoinColumn(onDelete="SET NULL")
     * @JMS\Type("AppBundle\Entity\MenuItem")
     * @JMS\Groups({"Default","MenuItem"})
     */
    protected $parent;

    public function getParent()
	{
		return $this->parent;
	}

	public function setParent($parent)
	{
		if($this->department !== null){
			throw new \Exception("Cannot have a parent and department.");
		}
		$this->parent = $parent;
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

	public function __construct() {
        $this->children = new ArrayCollection();
    }

    /**
     * @ORM\PreUpdate
     */
    public function onUpdate()
    {
        if($this->department !== null and $this->parent !== null){
			throw new \Exception("Cannot have a parent and department.");
		}
    }

    public function isOwnedByOrganization(Organization $organization)
    {
        return (
        	$this->getOrganization() === $organization and
        	(!$this->getDepartment() or $this->getDepartment()->isOwnedByOrganization($organization) ) and
			(!$this->getParent() or $this->getParent()->isOwnedByOrganization($organization) )
        );
    }
}
