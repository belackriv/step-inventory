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

	/**
	 * @ORM\Column(type="boolean")
     * @JMS\Type("boolean")
     */
	protected $isCompleted = null;

	public function getIsCompleted()
	{
		return $this->isCompleted;
	}

	public function setIsCompleted($isCompleted)
	{
		$this->isCompleted = $isCompleted;
		return $this;
	}

	public function end(Bin $deviationBin)
	{
		$totalDeviations = 0;
		$travelerIdCountDeviations = 0;
		$travelerIdMatchDeviations = 0;
		$skuCountDeviations = 0;

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

		foreach($this->inventorySkuAudits as $skuAudit){
			$totalDeviations += abs($skuAudit->getUserCount() - $skuAudit->getSystemCount());
			$skuCountDeviations += abs($skuAudit->getUserCount() - $skuAudit->getSystemCount());
		}

		$this->setSkuCountDeviations($skuCountDeviations);
		$this->setTotalDeviations($totalDeviations);
		$this->setIsCompleted(true);

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

	protected $skuCountDeviations = null;

	public function getSkuCountDeviations()
	{
		return $this->skuCountDeviations;
	}

	public function setSkuCountDeviations($skuCountDeviations)
	{
		$this->skuCountDeviations = $skuCountDeviations;
		return $this;
	}

	/**
     * @ORM\OneToMany(targetEntity="InventoryTravelerIdAudit", mappedBy="inventoryAudit", cascade={"merge"})
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
    public function addInventoryTravelerIdAudit(InventoryTravelerIdAudit $inventoryTravelerIdAudit)
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
     * @ORM\OneToMany(targetEntity="InventorySkuAudit", mappedBy="inventoryAudit", cascade={"merge"})
     * @ORM\OrderBy({"id" = "ASC"})
     * @JMS\Type("ArrayCollection<AppBundle\Entity\InventorySkuAudit>")
     */
    protected $inventorySkuAudits;

    public function getInventorySkuAudits()
    {
    	return $this->inventorySkuAudits;
    }

    /**
     * Add inventorySkuAudit
     *
     * @param \AppBundle\Entity\InventorySkuAudit $inventorySkuAudit
     * @return InventoryAudit
     */
    public function addInventorySkuAudit(InventorySkuAudit $inventorySkuAudit)
    {
        if (!$this->inventorySkuAudits->contains($inventorySkuAudit)) {
            $this->inventorySkuAudits->add($inventorySkuAudit);
        }
        if($inventorySkuAudit->getInventoryAudit() != $this){
            $inventorySkuAudit->setInventoryAudit($this);
        }
        return $this;
    }

    /**
     * Remove inventorySkuAudit
     *
     * @param \AppBundle\Entity\InventorySkuAudit $inventorySkuAudit
     * @return InventoryAudit
     */
    public function removeInventorySkuAudit(InventorySkuAudit $inventorySkuAudit)
    {
         if ($this->inventorySkuAudits->contains($inventorySkuAudit)) {
            $this->inventorySkuAudits->removeElement($inventorySkuAudit);
        }
        if($inventorySkuAudit->getInventoryAudit() !== null){
        	$inventorySkuAudit->setInventoryAudit(null);
        }
        return $this;
    }

    public function __construct() {
    	$this->inventoryTravelerIdAudits = new ArrayCollection();
        $this->inventorySkuAudits = new ArrayCollection();
    }

    public function isOwnedByOrganization(Organization $organization)
    {
        return (
			$this->getByUser() and $this->getByUser()->isOwnedByOrganization($organization) and
         	$this->getForBin() and  $this->getForBin()->isOwnedByOrganization($organization)
        );
    }

}