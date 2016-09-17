<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
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

	public function end(Bin $deviationBin)
	{
		$totalDeviations = 0;
		$travelerIdCountDeviations = 0;
		$travelerIdMatchDeviations = 0;
		$partCountDeviations = 0;

		$inventoryMovements = [];
		$scannedTravelerIds = [];

		$travelerIdCountDeviations = abs($this->inventoryTravelerIdAudits->count() - $this->getForBin()->getTravelerIds()->count());

		foreach($this->inventoryTravelerIdAudits as $travelerIdAudit){
			$travelerId = $travelerIdAudit->getTravelerId();
			$scannedTravelerIds[] = $travelerId;
			if($travelerId->getBin() !== $this->getForBin()){
				$travelerIdMatchDeviations++;
				$inventoryMovements[] = $this->createTravelerIdMovementIntoForBin($travelerId);
				$travelerId->setBin($this->getForBin());
			}
		}

		foreach($this->getForBin()->getTravelerIds() as $travelerId){
			if(!in_array($travelerId, $scannedTravelerIds)){
				$travelerIdMatchDeviations++;
				$inventoryMovements[] = $this->createTravelerIdMovementIntoDeviation($travelerId, $deviationBin);
				$travelerId->setBin($deviationBin);
			}
		}

		$totalDeviations = $travelerIdCountDeviations + $travelerIdMatchDeviations;

		foreach($this->inventoryPartAudits as $partAudit){
			$totalDeviations += abs($partAudit->getUserCount() - $partAudit->getSystemCount());
			$partCountDeviations += abs($partAudit->getUserCount() - $partAudit->getSystemCount());
		}

		$this->setPartCountDeviations($partCountDeviations);
		$this->setTotalDeviations($totalDeviations);

		return $inventoryMovements;
	}

	public function createTravelerIdMovementIntoForBin(TravelerId $travelerId)
	{
		$move = new InventoryTravelerIdMovement();
        $move->setTravelerId($travelerId);
        $move->setByUser($this->getByUser());
        $move->setMovedAt(new \DateTime());
        $move->setFromBin($travelerId->getBin());
        $move->setToBin($this->getForBin());
        $move->addTag('audit');
		return $move;
	}

	public function createTravelerIdMovementIntoDeviation(TravelerId $travelerId, Bin $deviationBin)
	{
		$move = new InventoryTravelerIdMovement();
        $move->setTravelerId($travelerId);
        $move->setByUser($this->getByUser());
        $move->setMovedAt(new \DateTime());
        $move->setFromBin($this->getForBin());
        $move->setToBin($deviationBin);
        $move->addTag('audit');
		return $move;
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

	protected $travelerIdCountDeviations = null;

	public function getTravelerIdCountDeviations()
	{
		return $this->travelerIdCountDeviations;
	}

	public function setTravelerIdCountDeviations($travelerIdCountDeviations)
	{
		$this->travelerIdCountDeviations = $travelerIdCountDeviations;
		return $this;
	}

	/**
	 * @ORM\Column(type="smallint", nullable=true)
	 * @JMS\Type("integer")
	 */

	protected $travelerIdMatchDeviations = null;

	public function getTravelerIdMatchDeviations()
	{
		return $this->travelerIdMatchDeviations;
	}

	public function setTravelerIdMatchDeviations($travelerIdMatchDeviations)
	{
		$this->travelerIdMatchDeviations = $travelerIdMatchDeviations;
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
     * @ORM\OneToMany(targetEntity="InventoryTravelerIdAudit", mappedBy="inventoryAudit")
     * @ORM\OrderBy({"id" = "ASC"})
     * @JMS\Type("ArrayCollection<AppBundle\Entity\InventoryTravelerIdAudit>")
     */
    protected $inventoryTravelerIdAudits;

    public function getInventoryTravelerIdAudits()
    {
    	return $this->inventoryTravelerIdAudits;
    }

    /**
     * Add inventoryTravelerIdAudit
     *
     * @param \AppBundle\Entity\InventoryTravelerIdAudit $inventoryTravelerIdAudit
     * @return InventoryAudit
     */
    public function addInventoryTravelerIdAudit(\AppBundle\Entity\InventoryTravelerIdAudit $inventoryTravelerIdAudit)
    {
        if (!$this->inventoryTravelerIdAudits->contains($inventoryTravelerIdAudit)) {
            $this->inventoryTravelerIdAudits->add($inventoryTravelerIdAudit);
        }
        if($inventoryTravelerIdAudit->getInventoryAudit() != $this){
            $inventoryTravelerIdAudit->setInventoryAudit($this);
        }
        return $this;
    }

    /**
     * Remove inventoryTravelerIdAudit
     *
     * @param \AppBundle\Entity\InventoryTravelerIdAudit $inventoryTravelerIdAudit
     * @return InventoryAudit
     */
    public function removeInventoryTravelerIdAudit(InventoryTravelerIdAudit $inventoryTravelerIdAudit)
    {
         if ($this->inventoryTravelerIdAudits->contains($inventoryTravelerIdAudit)) {
            $this->inventoryTravelerIdAudits->removeElement($inventoryTravelerIdAudit);
        }
        if($inventoryTravelerIdAudit->getInventoryAudit() !== null){
        	$inventoryTravelerIdAudit->setInventoryAudit(null);
        }
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
    	$this->inventoryTravelerIdAudits = new ArrayCollection();
        $this->inventoryPartAudits = new ArrayCollection();
    }

}