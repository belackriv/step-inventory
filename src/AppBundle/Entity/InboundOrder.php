<?php

namespace AppBundle\Entity;

use AppBundle\Library\Utilities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation As JMS;

/**
 * @ORM\Entity
 * @ORM\Table()
 */
Class InboundOrder
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

	public function generateLabel(array $inboundOrders)
	{
		$label = Utilities::baseEncode(array_search($this->id, array_column($inboundOrders, 'id'), true)+1);
		$this->setLabel($label);
		return $label;
	}

	/**
	 * @ORM\ManyToOne(targetEntity="Client", inversedBy="inboundOrders")
	 * @ORM\JoinColumn(nullable=false)
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
	 * @ORM\Column(type="boolean")
     * @JMS\Type("boolean")
     */
	protected $isReceived = false;

	public function getIsReceived()
	{
		return $this->isReceived;
	}

	public function setIsReceived($isReceived)
	{
		$this->isReceived = $isReceived;
		return $this;
	}

	/**
	 * @ORM\Column(type="datetime", nullable=true)
     * @JMS\Type("DateTime")
     */
	protected $expectedAt;

	public function getExpectedAt()
	{
		return $this->expectedAt;
	}

	public function setExpectedAt(\DateTime $expectedAt)
	{
		$this->expectedAt = $expectedAt;
		return $this;
	}

	/**
	 * @ORM\Column(type="datetime", nullable=true)
     * @JMS\Type("DateTime")
     */
	protected $receivedAt;

	public function getReceivedAt()
	{
		return $this->receivedAt;
	}

	public function setReceivedAt(\DateTime $receivedAt)
	{
		$this->receivedAt = $receivedAt;
		return $this;
	}

	/**
     * @ORM\OneToMany(targetEntity="TravelerId", mappedBy="inboundOrder")
     * @JMS\Type("ArrayCollection<AppBundle\Entity\TravelerId>")
     * @JMS\Groups({"OrderManifest"})
     * @JMS\ReadOnly
     */
    protected $travelerIds;

    public function getTravelerIds()
    {
    	return $this->travelerIds;
    }

    /**
     * @JMS\VirtualProperty
     */
     public function travelerIdCount()
     {
        return count($this->travelerIds);
     }

    public function addTravelerId(TravelerId $travelerId)
    {
        if(!$this->travelerIds->contains($travelerId)){
            $this->travelerIds->add($travelerId);
        }
        if($travelerId->getInboundOrder() !== $this){
        	$travelerId->setInboundOrder($this);
        }
        return $this;
    }

    public function removeTravelerId(TravelerId $travelerId)
    {
        $this->travelerIds->removeElement($travelerId);
        $this->travelerIds = new ArrayCollection(array_values($this->travelerIds->toArray()));
    }

    public function isOwnedByOrganization(Organization $organization)
    {
        return ( $this->getClient() and $this->getClient()->isOwnedByOrganization($organization) );
    }

    public function getSelectOptionData()
	{
		return [
			'id' => $this->id,
			'label' => $this->label,
			'isVoid' => $this->isVoid
		];
	}
}
