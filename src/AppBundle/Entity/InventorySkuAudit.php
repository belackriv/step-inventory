<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation As JMS;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table()
 */
Class InventorySkuAudit
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
	 * @ORM\ManyToOne(targetEntity="InventoryAudit", inversedBy="inventorySkuAudits")
	 * @JMS\Type("AppBundle\Entity\InventoryAudit")
	 */

	protected $inventoryAudit = null;

	public function getInventoryAudit()
	{
		return $this->inventoryAudit;
	}

	public function setInventoryAudit(InventoryAudit $inventoryAudit = null)
	{
		if($inventoryAudit === null){
			if($this->inventoryAudit->contains($this)){
				$this->inventoryAudit->removeInventorySkuAudit($this);
			}
			$this->inventoryAudit = null;
		}else{
			$this->inventoryAudit = $inventoryAudit;
			$inventoryAudit->addInventorySkuAudit($this);
		}
		return $this;
	}


	/**
	 * @ORM\ManyToOne(targetEntity="Sku")
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
	 * @ORM\Column(type="smallint", nullable=false)
     * @JMS\Type("integer")
     */
	protected $userCount = null;

	public function getUserCount()
	{
		return $this->userCount;
	}

	public function setUserCount($userCount)
	{
		$this->userCount = $userCount;
		return $this;
	}

	/**
	 * @ORM\Column(type="smallint", nullable=false)
     * @JMS\Type("integer")
     */
	protected $systemCount = null;

	public function getSystemCount()
	{
		return $this->systemCount;
	}

	public function setSystemCount($systemCount)
	{
		$this->systemCount = $systemCount;
		return $this;
	}

	public function isValid(User $user)
	{
		if($this->getSku() === null){
            throw new \Exception('Sku Must Be Set.');
        }
        foreach($this->inventoryAudit->getInventorySkuAudits() as $inventorySkuAudit){
        	if($inventorySkuAudit !== $this and $inventorySkuAudit->getSku() === $this->getSku()){
        		throw new \Exception('This SKU already has a count in this audit.');
        	}
        }
		if($this->getUserCount() === null){
            throw new \Exception('Count Must Be Set.');
        }
        if($this->inventoryAudit->getByUser() !== $user){
        	throw new \Exception('Count update an audit not started by yourself.');
        }
         if($this->inventoryAudit->getEndedAt()){
        	throw new \Exception('Count update an audit that has ended.');
        }
        return true;
	}

	/**
     * @ORM\PrePersist
     */
    public function onCreate()
    {
    	if($this->getUserCount() === null){
    		$this->setUserCount(0);
    	}
    }

	public function isOwnedByOrganization(Organization $organization)
	{
		return (
			$this->getInventoryAudit() and $this->getInventoryAudit()->isOwnedByOrganization($organization) and
			$this->getSku() and $this->getSku()->isOwnedByOrganization($organization)
		);
	}
}