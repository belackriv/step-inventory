<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation As JMS;

/**
 * @ORM\Entity
 * @ORM\Table()
 */
Class Client
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
	 * @ORM\ManyToOne(targetEntity="Organization", inversedBy="clients")
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

	public function isOwnedByOrganization(Organization $organization)
    {
        return ( $this->getOrganization() === $organization );
    }

    /**
     * @ORM\OneToMany(targetEntity="InboundOrder", mappedBy="client")
     * @JMS\Type("ArrayCollection<AppBundle\Entity\InboundOrder>")
     * @JMS\Exclude
     */
    protected $inboundOrders;

    public function getInboundOrders()
    {
        return $this->inboundOrders;
    }

    /**
     * @ORM\OneToMany(targetEntity="Contact", mappedBy="client")
     * @JMS\Type("ArrayCollection<AppBundle\Entity\Contact>")
     * @JMS\ReadOnly
     */
    protected $contacts;

    public function getContacts()
    {
        return $this->contacts;
    }

    /**
     * @ORM\OneToMany(targetEntity="Address", mappedBy="client")
     * @JMS\Type("ArrayCollection<AppBundle\Entity\Address>")
     * @JMS\ReadOnly
     */
    protected $addresses;

    public function getAddresses()
    {
        return $this->addresses;
    }
}
