<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation As JMS;

/**
 * @ORM\Entity
 * @ORM\Table()
 * @ORM\HasLifecycleCallbacks
 */
Class InventorySkuTransform extends InventoryTransform
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
	 * @ORM\ManyToOne(targetEntity="BinSkuCount")
	 * @ORM\JoinColumn(nullable=false)
	 * @JMS\Type("AppBundle\Entity\BinSkuCount")
	 */

	protected $fromBinSkuCount = null;

	public function getFromBinSkuCount()
	{
		return $this->fromBinSkuCount;
	}

	public function setFromBinSkuCount(BinSkuCount $fromBinSkuCount)
	{
		$this->fromBinSkuCount = $fromBinSkuCount;
		return $this;
	}

	/**
	 * @ORM\OneToOne(targetEntity="SalesItem")
	 * @ORM\JoinColumn(nullable=false)
	 * @JMS\Type("AppBundle\Entity\SalesItem")
	 */

	protected $toSalesItem = null;

	public function getToSalesItem()
	{
		return $this->toSalesItem;
	}

	public function setToSalesItem(SalesItem $toSalesItem)
	{
		$this->toSalesItem = $toSalesItem;
		return $this;
	}

	public function isOwnedByOrganization(Organization $organization)
    {
        return (
			$this->getFromBinSkuCount() and $this->getFromBinSkuCount()->isOwnedByOrganization($organization)
		);
    }

	public function setIsVoid($isVoid)
	{
		$this->isVoid = (boolean)$isVoid;
		$this->toSalesItem->setIsVoid($this->isVoid);
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

}