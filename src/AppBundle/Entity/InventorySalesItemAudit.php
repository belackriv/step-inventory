<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation As JMS;

/**
 * @ORM\Entity
 * @ORM\Table()
 */
Class InventorySalesItemAudit
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
	 * @ORM\ManyToOne(targetEntity="InventoryAudit", inversedBy="inventorySalesItemAudits")
	 * @JMS\Type("AppBundle\Entity\InventoryAudit")
	 */

	protected $inventoryAudit = null;

	public function getInventoryAudit()
	{
		return $this->inventoryAudit;
	}

	public function setInventoryAudit(InventoryAudit $inventoryAudit)
	{
		if($inventoryAudit === null){
			$this->inventoryAudit = null;
			$inventoryAudit->removeInventorySalesItemAudit($this);
		}else{
			$this->inventoryAudit = $inventoryAudit;
			$inventoryAudit->addInventorySalesItemAudit($this);
		}
		return $this;
	}

	/**
	 * @ORM\Column(type="string", length=64, unique=true)
     * @JMS\Type("string")
     */
	protected $salesItemLabel = null;

	public function getSalesItemLabel()
	{
		return $this->salesItemLabel;
	}

	public function setSalesItemLabel($salesItemLabel)
	{
		$this->salesItemLabel = $salesItemLabel;
		return $this;
	}

	/**
	 * @ORM\ManyToOne(targetEntity="SalesItem")
	 * @ORM\JoinColumn(nullable=false)
	 * @JMS\Type("AppBundle\Entity\SalesItem")
	 */

	protected $salesItem = null;

	public function getSalesItem()
	{
		return $this->salesItem;
	}

	public function setSalesItem(SalesItem $salesItem)
	{
		$this->salesItem = $salesItem;
		return $this;
	}


	public function isValid(User $user)
	{
		if($this->getSalesItemLabel() === null){
            throw new \Exception('SalesItem Label Must Be Set.');
        }
        if($this->getSalesItem() === null){
            throw new \Exception('No SalesItem Found.');
        }
        foreach($this->inventoryAudit->getInventorySalesItemAudits() as $inventorySalesItemAudit){
        	if($inventorySalesItemAudit !== $this and $inventorySalesItemAudit->getSalesItem() === $this->getSalesItem()){
        		throw new \Exception('This SalesItem already has been included in this audit.');
        	}
        }
        if($this->inventoryAudit->getByUser() !== $user){
        	throw new \Exception('Count update an audit not started by yourself.');
        }
         if($this->inventoryAudit->getEndedAt()){
        	throw new \Exception('Count update an audit that has ended.');
        }
        return true;
	}

	public function isOwnedByOrganization(Organization $organization)
	{
		return (
			$this->getInventoryAudit() and $this->getInventoryAudit()->isOwnedByOrganization($organization)
		);
	}
}