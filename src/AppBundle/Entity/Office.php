<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation As JMS;

/**
 * @ORM\Entity
 * @ORM\Table()
 */
Class Office
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
	 * @ORM\Column(type="string", length=32)
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
     * @ORM\ManyToOne(targetEntity="Organization", inversedBy="offices")
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
     * @ORM\OneToMany(targetEntity="Department", mappedBy="office")
     * @JMS\Type("ArrayCollection<AppBundle\Entity\Department>")
     * @JMS\Groups({"ListOffices"})
     */
    protected $departments;

    public function getDepartments()
    {
    	return $this->departments;
    }

    public function addDepartment(Department $department)
    {
    	$this->departments->add($department);
    	$department->setOffice($this);
    	return $this;
    }

    /**
     * Remove departments
     *
     * @param \AppBundle\Entity\Department $departments
     */
    public function removeDepartment(Department $departments)
    {
        $this->departments->removeElement($departments);
    }

    public function __construct() {
        $this->departments = new ArrayCollection();
    }

    public function isOwnedByOrganization(Organization $organization)
    {
        return ( $this->getOrganization() === $organization );
    }
}
