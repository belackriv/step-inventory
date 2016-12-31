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
	 * @ORM\ManyToOne(targetEntity="OutboundOrder", inversedBy="salesItems")
	 * @ORM\JoinColumn(nullable=true)
	 * @JMS\Type("AppBundle\Entity\OutboundOrder")
	 */
	protected $outboundOrder = null;

	public function getOutboundOrder()
	{
		return $this->outboundOrder;
	}

	public function setOutboundOrder(OutboundOrder $outboundOrder = null)
	{
		if($outboundOrder){
			$this->outboundOrder = $outboundOrder;
			$outboundOrder->addSalesItem($this);
		}else{
			if($this->outboundOrder and !$this->getSalesItems()->contains($this)){
				$this->outboundOrder->removeSalesItem($this);
			}
			$this->outboundOrder = $outboundOrder;
		}
		return $this;
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
		$salesItems = $this->getSku()->getOrganization()->getSalesItems();
		if(!$salesItems->contains($this)){
            $salesItems->add($this);
        }

        $iterator = $salesItems->getIterator();
		$iterator->uasort(function ($a, $b) {
		    return ($a->getId() < $b->getId()) ? -1 : 1;
		});
		$salesItems = new ArrayCollection(iterator_to_array($iterator));

		$label = 'SI-'.Utilities::baseEncode($salesItems->indexOf($this)+1,6);
		$this->setLabel($label);
		return $label;
	}

	/**
	 * @ORM\ManyToOne(targetEntity="Bin", inversedBy="salesItems")
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
	 * @ORM\Column(type="decimal", precision=7, scale=2, nullable=false)
	 * @JMS\Type("float")
	 */
	protected $quantity;

	public function getQuantity()
	{
		return $this->quantity;
	}

	public function setQuantity($quantity)
	{
		$this->quantity = $quantity;
		return $this;
	}

	/**
	 * @ORM\Column(type="decimal", precision=7, scale=2, nullable=true)
	 * @JMS\Type("float")
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
	 * @ORM\ManyToOne(targetEntity="InventoryTravelerIdTransform", inversedBy="toSalesItems")
	 * @ORM\JoinColumn(nullable=true)
	 * @JMS\Type("AppBundle\Entity\InventoryTravelerIdTransform")
	 */
	protected $reverseTransform = null;

	public function getReverseTransform()
	{
		return $this->reverseTransform;
	}

	public function setReverseTransform(InventoryTravelerIdTransform $transform = null)
	{
		if($transform and !$transform->getToSalesItems()->contains($this)){
			$this->reverseTransform = $transform;
			$transform->addToSalesItem($this);
		}else if(	$transform === null and $this->reverseTransform and
					$this->reverseTransform->getToSalesItems()->contains($this)){
			$this->reverseTransform->removeToSalesItem($this);
			$this->reverseTransform = $transform;
		}else{
			$this->reverseTransform = $transform;
		}
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
    	$this->generateLabel();
    }

    public function isOwnedByOrganization(Organization $organization)
    {
        return (
        	(!$this->getOutboundOrder() or $this->getOutboundOrder()->isOwnedByOrganization($organization)) and
			$this->getBin() and $this->getBin()->isOwnedByOrganization($organization) and
			$this->getSku() and $this->getSku()->isOwnedByOrganization($organization)
		);
    }

    public function assignPropertiesFromDataTransferObject(SalesItemDataTransferObject $dto)
    {
    	$this->setOutboundOrder($dto->outboundOrder);
		$this->setLabel($dto->label);
		$this->setBin($dto->bin);
		$this->setIsVoid($dto->isVoid);
		$this->setRevenue($dto->revenue);
		return $this;
    }

    public function __toString()
    {
    	return (string)$this->label;
    }
}
