<?php

namespace AppBundle\Entity;

use AppBundle\Library\Utilities;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation As JMS;

/**
 * @ORM\Entity
 * @ORM\Table()
 */
Class OutboundOrder
{
	public function __construct()
    {
        $this->travelerIds = new ArrayCollection();
    }

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
	protected $label = null;

	public function getLabel()
	{
		return $this->label;
	}

	public function setLabel($label)
	{
		$this->label = $label;
		return $this;
	}

	public function generateLabel()
	{
		$label = Utilities::baseEncode($this->getId());
		$this->setLabel($label);
		return $label;
	}

	/**
	 * @ORM\ManyToOne(targetEntity="Customer")
	 * @ORM\JoinColumn(nullable=false)
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
	 * @ORM\Column(type="boolean")
     * @JMS\Type("boolean")
     */
	protected $isVoid = false;

	public function getIsVoid()
	{
		return $this->isVoid;
	}

	public function setIsVoid($isVoid)
	{
		$this->isVoid = $isVoid;
		return $this;
	}

	/**
     * @ORM\OneToMany(targetEntity="TravelerId", mappedBy="outboundOrder")
     * @JMS\Type("ArrayCollection<AppBundle\Entity\TravelerId>")
     * @JMS\Groups({"TravelerId"})
     * @JMS\ReadOnly
     */
    protected $travelerIds;

    public function getTravelerIds()
    {
    	return $this->travelerIds;
    }

    public function addTravelerId(TravelerId $travelerId)
    {
        if(!$this->travelerIds->contains($travelerId)){
            $this->travelerIds->add($travelerId);
        }
        if($travelerId->getOutboundOrder() !== $this){
        	$travelerId->setOutboundOrder($this);
        }
        return $this;
    }

    public function removeTravelerId(TravelerId $travelerId)
    {
        $this->travelerIds->removeElement($travelerId);
        $travelerId->setOutboundOrder(null);
        $this->travelerIds = new ArrayCollection(array_values($this->children->toArray()));
    }

}
