<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation As JMS;

/**
 * @ORM\Entity
 * @ORM\Table()
 */
Class Department
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
	 * @ORM\Column(type="string", length=32)
     * @JMS\Type("string")
     * @JMS\Groups({"Default","MenuItem"})
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
	 * @ORM\ManyToOne(targetEntity="Office", inversedBy="departments")
	 * @JMS\Type("AppBundle\Entity\Office")
     * @JMS\Exclude
	 */
//JMS\Groups({"GetMyself"})
	protected $office = null;

	public function getOffice()
	{
		return $this->office;
	}

	public function setOffice($office)
	{
		$this->office = $office;
		return $this;
	}

	/**
     * @ORM\OneToMany(targetEntity="MenuItem", mappedBy="department")
     * @ORM\OrderBy({"position" = "ASC"})
     * @JMS\Type("ArrayCollection<AppBundle\Entity\MenuItem>")
     * @JMS\Groups({"ListOffices"})
     */
    protected $menuItems;

    public function getMenuItems()
    {
    	return $this->menuItems;
    }

    public function setMenuItems(ArrayCollection $menuItems)
    {
    	$this->menuItems = $menuItems;

    	return $this;
    }

    /**
     * Add menuItems
     *
     * @param \AppBundle\Entity\MenuItem $menuItem
     * @return Department
     */
    public function addMenuItem(\AppBundle\Entity\MenuItem $menuItem)
    {
        $this->menuItems[] = $menuItem;
        $menuItem->setDepartment($this);
        return $this;
    }

    /**
     * Remove menuItems
     *
     * @param \AppBundle\Entity\MenuItem $menuItem
     */
    public function removeMenuItem(MenuItem $menuItem)
    {
        $this->menuItems->removeElement($menuItem);
        $menuItem->setDepartment(null);
        $this->menuItems = new ArrayCollection(array_values($this->menuItems->toArray()));
    }

    public function __construct() {
        $this->menuItems = new ArrayCollection();
    }

    public function getOrganization()
    {
        $this->getOffice()->getOrganization();
    }
}
