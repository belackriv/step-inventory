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
Class OutboundOrder
{
	public function __construct()
    {
        $this->salesItems = new ArrayCollection();
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
		$outboundOrders = $this->getCustomer()->getOrganization()->getOutboundOrders();
		if(!$outboundOrders->contains($this)){
            $outboundOrders->add($this);
        }

        $iterator = $outboundOrders->getIterator();
		$iterator->uasort(function ($a, $b) {
		    return ($a->getId() < $b->getId()) ? -1 : 1;
		});
		$outboundOrders = new ArrayCollection(iterator_to_array($iterator));

		$label = Utilities::baseEncode($outboundOrders->indexOf($this)+1);
		$this->setLabel($label);
		return $label;
	}

	/**
	 * @ORM\ManyToOne(targetEntity="Customer", inversedBy="outboundOrders")
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
	 * @ORM\Column(type="boolean")
     * @JMS\Type("boolean")
     */
	protected $isShipped = false;

	public function getIsShipped()
	{
		return $this->isShipped;
	}

	public function setIsShipped($isShipped)
	{
		$this->isShipped = $isShipped;
		return $this;
	}

	/**
     * @ORM\OneToMany(targetEntity="SalesItem", mappedBy="outboundOrder")
     * @JMS\Type("ArrayCollection<AppBundle\Entity\SalesItem>")
     * @JMS\Groups({"OrderManifest"})
     * @JMS\ReadOnly
     */
    protected $salesItems;

    public function getSalesItems()
    {
    	return $this->salesItems;
    }

    /**
     * @JMS\VirtualProperty
     */
     public function salesItemCount()
     {
        return count($this->salesItems);
     }

    public function addSalesItem(SalesItem $salesItem)
    {
        if(!$this->salesItems->contains($salesItem)){
            $this->salesItems->add($salesItem);
        }
        if($salesItem->getOutboundOrder() !== $this){
        	$salesItem->setOutboundOrder($this);
        }
        return $this;
    }

    public function removeSalesItem(SalesItem $salesItem)
    {
        $this->salesItems->removeElement($salesItem);
        $this->salesItems = new ArrayCollection(array_values($this->salesItems->toArray()));
    }

    public function isOwnedByOrganization(Organization $organization)
    {
        return ( $this->getCustomer() and $this->getCustomer()->isOwnedByOrganization($organization) );
    }

    public function getSelectOptionData()
	{
		return [
			'id' => $this->id,
			'label' => $this->label,
			'isVoid' => $this->isVoid
		];
	}

	public function ship(User $byUser, Bin $shippedBin)
	{
		$moves = [];
		foreach($this->getSalesItems() as $salesItem){
			if(!$salesItem->getIsVoid()){
				$moves[] = $this->createSalesItemMovementIntoShippedBin($byUser, $salesItem, $shippedBin);
				$salesItem->setBin($shippedBin);
			}
		}
		$this->setIsShipped(true);
		return $moves;
	}

	public function createSalesItemMovementIntoShippedBin(User $byUser, SalesItem $salesItem, Bin $shippedBin)
	{
		$move = new InventorySalesItemMovement();
        $move->setSalesItem($salesItem);
        $move->setByUser($byUser);
        $move->setMovedAt(new \DateTime());
        $move->setFromBin($salesItem->getBin());
        $move->setToBin($shippedBin);
        $move->addTag('shipped');
		return $move;
	}
}
