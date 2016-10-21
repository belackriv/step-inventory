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
Class SalesItem
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
	 * @ORM\ManyToOne(targetEntity="OutboundOrder", inversedBy="travelerIds")
	 * @ORM\JoinColumn(nullable=false)
	 * @JMS\Type("AppBundle\Entity\OutboundOrder")
	 */

	protected $outboundOrder = null;

	public function getOutboundOrder()
	{
		return $this->outboundOrder;
	}

	public function setOutboundOrder(OutboundOrder $outboundOrder = null)
	{
		$this->outboundOrder = $outboundOrder;
		$outboundOrder->addSalesItem($this);
		return $this;
	}

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
    	if($this->getIsVoid() === null){
    		$this->setIsVoid(false);
    	}
    }

    public function isOwnedByOrganization(Organization $organization)
    {
        return (
        	$this->getOutboundOrder() and $this->getOutboundOrder()->isOwnedByOrganization($organization) and
			$this->getBin() and $this->getBin()->isOwnedByOrganization($organization) and
			$this->getSku() and $this->getSku()->isOwnedByOrganization($organization)
		);
    }
}
