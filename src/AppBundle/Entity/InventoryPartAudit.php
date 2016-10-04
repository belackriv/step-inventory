<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation As JMS;

/**
 * @ORM\Entity
 * @ORM\Table()
 */
Class InventoryPartAudit
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
	 * @ORM\ManyToOne(targetEntity="InventoryAudit", inversedBy="inventoryPartAudits")
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
			$inventoryAudit->removeInventoryPartAudit($this);
		}else{
			$this->inventoryAudit = $inventoryAudit;
			$inventoryAudit->addInventoryPartAudit($this);
		}
		return $this;
	}


	/**
	 * @ORM\ManyToOne(targetEntity="Part")
	 * @ORM\JoinColumn(nullable=false)
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
		if($this->getPart() === null){
            throw new \Exception('Part Must Be Set.');
        }
        foreach($this->inventoryAudit->getInventoryPartAudits() as $inventoryPartAudit){
        	if($inventoryPartAudit !== $this and $inventoryPartAudit->getPart() === $this->getPart()){
        		throw new \Exception('This Part already has a count in this audit.');
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

	public function isOwnedByOrganization(Organization $organization)
	{
		return (
			$this->getInventoryAudit()->isOwnedByOrganization($organization) and
			$this->getPart()->isOwnedByOrganization($organization)
		);
	}
}