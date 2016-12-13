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
     * @JMS\Groups({"SalesItem"})
     * @JMS\ReadOnly
     */
    protected $salesItems;

    public function getSalesItems()
    {
    	return $this->salesItems;
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
}
