<?php

namespace AppBundle\Entity;

use AppBundle\Library\Utilities;

use Ramsey\Uuid\Uuid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation As JMS;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table()
 */
Class TravelerId
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
	 * @ORM\ManyToOne(targetEntity="InboundOrder", inversedBy="travelerIds")
	 * @ORM\JoinColumn(nullable=false)
	 * @JMS\Type("AppBundle\Entity\InboundOrder")
	 */

	protected $inboundOrder = null;

	public function getInboundOrder()
	{
		return $this->inboundOrder;
	}

	public function setInboundOrder(InboundOrder $inboundOrder)
	{
		$this->inboundOrder = $inboundOrder;
		$this->inboundOrder->addTravelerId($this);
		$this->generateLabel();
		return $this;
	}

	/**
	 * @ORM\ManyToOne(targetEntity="OutboundOrder", inversedBy="travelerIds")
	 * @ORM\JoinColumn(nullable=true)
	 * @JMS\Type("AppBundle\Entity\OutboundOrder")
	 */

	protected $outboundOrder = null;

	public function getOutboundOrder()
	{
		return $this->outboundOrder;
	}

	public function setOutboundOrder(OutboundOrder $outboundOrder)
	{
		$this->outboundOrder = $outboundOrder;
		$this->outboundOrder->addTravelerId($this);
		return $this;
	}

	/**
	 * @ORM\Column(type="string", length=64, unique=true)
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
		$tids = $this->getInboundOrder()->getTravelerIds();
		if(is_a($tids, 'Doctrine\ORM\PersistentCollection')){
			$tids->initialize();
		}
		$this->getInboundOrder()->addTravelerId($this);
		$label = $this->getInboundOrder()->getLabel().'-'.
			Utilities::baseEncode($tids->indexOf($this)+1);
		$this->setLabel($label);
		return $label;
	}

	/**
	 * @ORM\Column(type="string", length=64)
     * @JMS\Type("string")
     */
	protected $serial = null;

	public function getSerial()
	{
		return $this->serial;
	}

	public function setSerial($serial)
	{
		$this->serial = $serial;
		return $this;
	}

	public function generateSerial()
	{
		$serial = Uuid::uuid4();
		$this->setSerial($serial);
		return $serial;
	}

	/**
	 * @ORM\ManyToOne(targetEntity="Bin", inversedBy="travelerIds")
	 * @ORM\JoinColumn(nullable=false)
	 * @JMS\Type("AppBundle\Entity\Bin")
	 */

	protected $bin = null;

	public function getBin()
	{
		return $this->bin;
	}

	public function setBin(Bin $bin)
	{
		$this->bin = $bin;
		return $this;
	}

	/**
	 * @ORM\ManyToOne(targetEntity="Part", )
	 * @ORM\JoinColumn(nullable=true)
	 * @JMS\Type("AppBundle\Entity\Part")
	 */

	protected $part = null;

	public function getPart()
	{
		return $this->part;
	}

	public function setPart(Part $part)
	{
		$this->part = $part;
		return $this;
	}

	//will add Device and Comodity

	/**
	 * @ORM\Column(type="boolean")
     * @JMS\Type("boolean")
     */
	protected $isVoid = null;

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
	 * @ORM\Column(type="decimal", precision=7, scale=2, nullable=true)
	 * @JMS\Type("string")
	 */
	protected $cost;

	public function getCost()
	{
		return $this->cost;
	}

	public function setCost($cost)
	{
		$this->cost = $cost;
		return $this;
	}

	/**
	 * @ORM\Column(type="decimal", precision=7, scale=2, nullable=true)
	 * @JMS\Type("string")
	 */
	protected $revenue;

	public function getRevenue()
	{
		return $this->revenue;
	}

	public function setRevenue($revenue)
	{
		$this->revenue = $revenue;
		return $this;
	}

	 /**
     * @ORM\PrePersist
     */
    public function onCreate()
    {
    	if($this->getSerial() === null){
    		$this->generateSerial();
    	}
    	if($this->getIsVoid() === null){
    		$this->setIsVoid(false);
    	}
    }

    public function isOwnedByOrganization(Organization $organization)
    {
        return (
        	$this->getInboundOrder()->isOwnedByOrganization($organization) and
			$this->getBin()->isOwnedByOrganization($organization) and
			(!$this->getOutboundOrder() or $this->getOutboundOrder()->isOwnedByOrganization($organization) ) and
			(!$this->getPart() or $this->getPart()->isOwnedByOrganization($organization) )
		);
    }
}
