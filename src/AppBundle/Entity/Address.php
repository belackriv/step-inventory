<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation As JMS;

/**
 * @ORM\Entity
 * @ORM\Table()
 */
Class Address
{
	/** @const string */
    const TYPE_BILLING = 'billing';

    /** @const string */
    const TYPE_MAILING = 'mailing';

    /** @const string */
    const TYPE_SHIPPING = 'shipping';

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
	 * @ORM\Column(type="string", length=255)
     * @JMS\Type("string")
     */
	protected $street = null;

	public function getStreet()
	{
		return $this->street;
	}

	public function setStreet($street)
	{
		$this->street = $street;
		return $this;
	}

	/**
	 * @ORM\Column(type="string", nullable=true, length=255)
     * @JMS\Type("string")
     */
	protected $unit = null;

	public function getUnit()
	{
		return $this->unit;
	}

	public function setUnit($unit)
	{
		$this->unit = $unit;
		return $this;
	}

	/**
	 * @ORM\Column(type="string", length=255)
     * @JMS\Type("string")
     */
	protected $city = null;

	public function getCity()
	{
		return $this->city;
	}

	public function setCity($city)
	{
		$this->city = $city;
		return $this;
	}

	/**
	 * @ORM\Column(type="string", length=255)
     * @JMS\Type("string")
     */
	protected $state = null;

	public function getState()
	{
		return $this->state;
	}

	public function setState($state)
	{
		$this->state = $state;
		return $this;
	}

	/**
	 * @ORM\Column(type="string", length=255)
     * @JMS\Type("string")
     */
	protected $postalCode = null;

	public function getPostalCode()
	{
		return $this->postalCode;
	}

	public function setPostalCode($postalCode)
	{
		$this->postalCode = $postalCode;
		return $this;
	}

	/**
	 * @ORM\Column(type="string", length=255)
     * @JMS\Type("string")
     */
	protected $country = null;

	public function getCountry()
	{
		return $this->country;
	}

	public function setCountry($country)
	{
		$this->country = $country;
		return $this;
	}

	/**
	 * @ORM\Column(type="string", length=255)
     * @JMS\Type("string")
     */
	protected $type = null;

	public function getType()
	{
		return $this->type;
	}

	public function setType($type)
	{
		$this->type = $type;
		return $this;
	}

	/**
	 * @ORM\Column(type="text", nullable=true)
     * @JMS\Type("string")
     */
	protected $note = null;

	public function getNote()
	{
		return $this->note;
	}

	public function setNote($note)
	{
		$this->note = $note;
		return $this;
	}

	/**
	 * @ORM\ManyToOne(targetEntity="Client", inversedBy="addresses")
	 * @ORM\JoinColumn(nullable=true)
	 * @JMS\Type("AppBundle\Entity\Client")
	 */
	protected $client = null;

	public function getClient()
	{
		return $this->client;
	}

	public function setClient(Client $client)
	{
		$this->client = $client;
		return $this;
	}

	/**
	 * @ORM\ManyToOne(targetEntity="Customer", inversedBy="addresses")
	 * @ORM\JoinColumn(nullable=true)
	 * @JMS\Type("AppBundle\Entity\Customer")
	 */
	protected $customer = null;

	public function getCustomer()
	{
		return $this->customer;
	}

	public function setCustomer(Customer $customer)
	{
		$this->customer = $customer;
		return $this;
	}

	public function isOwnedByOrganization(Organization $organization)
    {
    	if($this->client){
    		return ($this->client->getOrganization() === $organization);
    	}
        if($this->customer){
        	return ($this->customer->getOrganization() === $organization);
        }
        return false;
    }

}
