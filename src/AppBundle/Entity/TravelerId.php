<?php

namespace AppBundle\Entity;

use AppBundle\Library\Utilities;

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
	 * @ORM\ManyToOne(targetEntity="Sku", )
	 * @ORM\JoinColumn(nullable=false)
	 * @JMS\Type("AppBundle\Entity\Sku")
	 */

	protected $sku = null;

	public function getSku()
	{
		return $this->sku;
	}

	public function setSku(Sku $sku)
	{
		$this->sku = $sku;
		return $this;
	}

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
     * @ORM\PrePersist
     */
    public function onCreate()
    {
    	if($this->getIsVoid() === null){
    		$this->setIsVoid(false);
    	}
    }

    public function isOwnedByOrganization(Organization $organization)
    {
        return (
        	$this->getInboundOrder() and $this->getInboundOrder()->isOwnedByOrganization($organization) and
			$this->getBin() and $this->getBin()->isOwnedByOrganization($organization) and
			$this->getSku() and $this->getSku()->isOwnedByOrganization($organization)
		);
    }
}
