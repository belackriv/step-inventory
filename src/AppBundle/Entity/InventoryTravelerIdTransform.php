<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation As JMS;

/**
 * @ORM\Entity
 * @ORM\Table()
 * @ORM\HasLifecycleCallbacks
 */
Class InventoryTravelerIdTransform extends InventoryTransform
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
	 * @ORM\OneToMany(targetEntity="TravelerId", mappedBy="transform", cascade={"merge","detach"})
	 * @JMS\Type("ArrayCollection<AppBundle\Entity\TravelerId>")
	 */

	protected $fromTravelerIds = null;

	public function getFromTravelerIds()
	{
		return $this->fromTravelerIds;
	}

	public function addFromTravelerId(TravelerId $travelerId)
    {
    	$this->fromTravelerIds->add($travelerId);
    	$travelerId->setTransform($this);
    	return $this;
    }

    public function removeFromTravelerId(TravelerId $travelerId)
    {
        $this->fromTravelerIds->removeElement($travelerId);
        $travelerId->setTransform(null);
        return $this;
    }

	/**
	 * @ORM\Column(type="decimal", precision=7, scale=5, nullable=false)
	 * @JMS\Type("string")
	 */
	protected $ratio;

	public function getRatio()
	{
		return $this->ratio;
	}

	public function setRatio($ratio)
	{
		$this->ratio = $ratio;
		return $this;
	}

	/**
	 * @ORM\OneToMany(targetEntity="TravelerId", mappedBy="reverseTransform", cascade={"merge","detach"})
	 * @JMS\Type("ArrayCollection<AppBundle\Entity\TravelerId>")
	 */

	protected $toTravelerIds = null;

	public function getToTravelerIds()
	{
		return $this->toTravelerIds;
	}

	public function addToTravelerId(TravelerId $travelerId)
    {
    	$this->toTravelerIds->add($travelerId);
    	$travelerId->setReverseTransform($this);
    	return $this;
    }

    public function removeToTravelerId(TravelerId $travelerId)
    {
        $this->toTravelerIds->removeElement($travelerId);
        $travelerId->setReverseTransform(null);
        return $this;
    }

	/**
	 * @ORM\OneToMany(targetEntity="SalesItem", mappedBy="reverseTransform", cascade={"merge","detach"})
	 * @JMS\Type("ArrayCollection<AppBundle\Entity\SalesItem>")
	 */

	protected $toSalesItems = null;

	public function getToSalesItems()
	{
		return $this->toSalesItems;
	}

	public function addToSalesItem(SalesItem $salesItem)
    {
    	$this->toSalesItems->add($salesItem);
    	$salesItem->setReverseTransform($this);
    	return $this;
    }

    public function removeToSalesItem(SalesItem $salesItem)
    {
        $this->toSalesItems->removeElement($salesItem);
        $salesItem->setReverseTransform(null);
        return $this;
    }

	public function isOwnedByOrganization(Organization $organization)
    {
        $isOwnedByOrganization = true;
        foreach($this->fromTravelerIds as $travelerId){
            if(!$travelerId->isOwnedByOrganization($organization)){
                $isOwnedByOrganization = false;
            }
        }
        foreach($this->toTravelerIds as $travelerId){
            if(!$travelerId->isOwnedByOrganization($organization)){
                $isOwnedByOrganization = false;
            }
        }
        foreach($this->toSalesItems as $salesItem){
            if(!$salesItem->isOwnedByOrganization($organization)){
                $isOwnedByOrganization = false;
            }
        }
        return $isOwnedByOrganization;
    }

   	public function setIsVoid($isVoid)
	{
		$this->isVoid = (boolean)$isVoid;
		foreach($this->getToTravelerIds() as $toTravelerId){
			$toTravelerId->setIsVoid($this->isVoid);
		}
		foreach($this->getToSalesItems() as $toSalesItem){
			$toSalesItem->setIsVoid($this->isVoid);
		}

		return $this;
	}

    public function __construct() {
        $this->fromTravelerIds = new ArrayCollection();
        $this->toTravelerIds = new ArrayCollection();
        $this->toSalesItems = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist
     */
    public function onCreate()
    {
    	if($this->getIsVoid() === null){
    		$this->setIsVoid(false);
    	}
		$this->calculateRatio();
    }

    /**
     * @ORM\PreUpdate
     */
    public function onUpdate()
    {
		$this->calculateRatio();
    }


    private function calculateRatio()
    {
    	$fromCount = $this->fromTravelerIds->count();
    	$toCount = ($this->toTravelerIds->count() > 0)?$this->toTravelerIds->count():$this->toSalesItems->count();
    	if($toCount === 0){
    		$this->ratio = null;
    	}else{
    		$this->ratio = $fromCount / $toCount;
    	}
    	if($fromCount > 1 and $this->toTravelerIds->count() > 0){
    		throw new \Exception("Must use sales items if consolidating Traveler Ids");
    	}
    }

}