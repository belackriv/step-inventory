<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation As JMS;

/**
 * @ORM\Entity
 * @ORM\Table()
 */
Class Contact
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
	 * @ORM\Column(type="string", length=255)
     * @JMS\Type("string")
     */
	protected $firstName = null;

	public function getFirstName()
	{
		return $this->firstName;
	}

	public function setFirstName($firstName)
	{
		$this->firstName = $firstName;
		return $this;
	}

	/**
	 * @ORM\Column(type="string", length=255)
     * @JMS\Type("string")
     */
	protected $lastName = null;

	public function getLastName()
	{
		return $this->lastName;
	}

	public function setLastName($lastName)
	{
		$this->lastName = $lastName;
		return $this;
	}

	/**
	 * @ORM\Column(type="string", length=255)
     * @JMS\Type("string")
     */
	protected $emailAddress = null;

	public function getEmailAddress()
	{
		return $this->emailAddress;
	}

	public function setEmailAddress($emailAddress)
	{
		$this->emailAddress = $emailAddress;
		return $this;
	}

	/**
	 * @ORM\Column(type="string", nullable=true, length=255)
     * @JMS\Type("string")
     */
	protected $phoneNumber = null;

	public function getPhoneNumber()
	{
		return $this->phoneNumber;
	}

	public function setPhoneNumber($phoneNumber)
	{
		$this->phoneNumber = $phoneNumber;
		return $this;
	}

	/**
	 * @ORM\Column(type="string", nullable=true, length=255)
     * @JMS\Type("string")
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
	 * @ORM\ManyToOne(targetEntity="Client", inversedBy="contacts")
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
	 * @ORM\ManyToOne(targetEntity="Customer", inversedBy="contacts")
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
