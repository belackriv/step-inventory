<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation As JMS;

/**
 * @ORM\Entity
 * @ORM\Table()
 */
Class InventoryAudit
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
	 * @ORM\ManyToOne(targetEntity="User")
	 * @ORM\JoinColumn(nullable=false)
	 * @JMS\Type("AppBundle\Entity\User")
	 */

	protected $byUser = null;

	public function getByUser()
	{
		return $this->byUser;
	}

	public function setByUser(User $user)
	{
		$this->byUser = $user;
		return $this;
	}

	/**
	 * @ORM\ManyToOne(targetEntity="Bin")
	 * @ORM\JoinColumn(nullable=false)
	 * @JMS\Type("AppBundle\Entity\Bin")
	 */

	protected $forBin = null;

	public function getForBin()
	{
		return $this->forBin;
	}

	public function setForBin(Bin $bin)
	{
		$this->forBin = $bin;
		return $this;
	}

	/**
	 * @ORM\Column(type="datetime", nullable=false)
	 * @JMS\Type("DateTime")
	 */

	protected $startedAt = null;

	public function getStartedAt()
	{
		return $this->startedAt;
	}

	public function setStartedAt(\DateTime $startedAt)
	{
		$this->startedAt = $startedAt;
		return $this;
	}

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 * @JMS\Type("DateTime")
	 */

	protected $endedAt = null;

	public function getEndedAt()
	{
		return $this->endedAt;
	}

	public function setEndedAt(\DateTime $endedAt)
	{
		$this->endedAt = $endedAt;
		return $this;
	}

	public function end()
	{
		$totalDeviations = 0;
		$serialCountDeviations = 0;
		$serialMatchDeviations = 0;
		$partCountDeviations = 0;

		foreach($this->inventoryPartAudits as $partAudit){
			$totalDeviations += abs($partAudit->getUserCount() - $partAudit->getSystemCount());
			$partCountDeviations += abs($partAudit->getUserCount() - $partAudit->getSystemCount());
		}

		$this->setPartCountDeviations($partCountDeviations);
		$this->setTotalDeviations($totalDeviations);
	}

	/**
	 * @ORM\Column(type="smallint", nullable=true)
	 * @JMS\Type("integer")
	 */

	protected $totalDeviations = null;

	public function getTotalDeviations()
	{
		return $this->totalDeviations;
	}

	public function setTotalDeviations($totalDeviations)
	{
		$this->totalDeviations = $totalDeviations;
		return $this;
	}

	/**
	 * @ORM\Column(type="smallint", nullable=true)
	 * @JMS\Type("integer")
	 */

	protected $serialCountDeviations = null;

	public function getSerialCountDeviations()
	{
		return $this->serialCountDeviations;
	}

	public function setSerialCountDeviations($serialCountDeviations)
	{
		$this->serialCountDeviations = $serialCountDeviations;
		return $this;
	}

	/**
	 * @ORM\Column(type="smallint", nullable=true)
	 * @JMS\Type("integer")
	 */

	protected $serialMatchDeviations = null;

	public function getSerialMatchDeviations()
	{
		return $this->serialMatchDeviations;
	}

	public function setSerialMatchDeviations($serialMatchDeviations)
	{
		$this->serialMatchDeviations = $serialMatchDeviations;
		return $this;
	}

	/**
	 * @ORM\Column(type="smallint", nullable=true)
	 * @JMS\Type("integer")
	 */

	protected $partCountDeviations = null;

	public function getPartCountDeviations()
	{
		return $this->partCountDeviations;
	}

	public function setPartCountDeviations($partCountDeviations)
	{
		$this->partCountDeviations = $partCountDeviations;
		return $this;
	}


	/**
     * @ORM\OneToMany(targetEntity="InventoryPartAudit", mappedBy="inventoryAudit")
     * @ORM\OrderBy({"id" = "ASC"})
     * @JMS\Type("ArrayCollection<AppBundle\Entity\InventoryPartAudit>")
     */
    protected $inventoryPartAudits;

    public function getInventoryPartAudits()
    {
    	return $this->inventoryPartAudits;
    }

    /**
     * Add inventoryPartAudit
     *
     * @param \AppBundle\Entity\InventoryPartAudit $inventoryPartAudit
     * @return InventoryAudit
     */
    public function addInventoryPartAudit(\AppBundle\Entity\InventoryPartAudit $inventoryPartAudit)
    {
        if (!$this->inventoryPartAudits->contains($inventoryPartAudit)) {
            $this->inventoryPartAudits->add($inventoryPartAudit);
        }
        if($inventoryPartAudit->getInventoryAudit() != $this){
            $inventoryPartAudit->setInventoryAudit($this);
        }
        return $this;
    }

    /**
     * Remove inventoryPartAudit
     *
     * @param \AppBundle\Entity\InventoryPartAudit $inventoryPartAudit
     * @return InventoryAudit
     */
    public function removeInventoryPartAudit(InventoryPartAudit $inventoryPartAudit)
    {
         if ($this->inventoryPartAudits->contains($inventoryPartAudit)) {
            $this->inventoryPartAudits->removeElement($inventoryPartAudit);
        }
        if($inventoryPartAudit->getInventoryAudit() !== null){
        	$inventoryPartAudit->setInventoryAudit(null);
        }
        return $this;
    }

    public function __construct() {
        $this->inventoryPartAudits = new ArrayCollection();
    }

}