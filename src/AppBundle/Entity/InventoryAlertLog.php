<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation As JMS;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity()
 * @ORM\Table()
 */
Class InventoryAlertLog
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
	 * @ORM\ManyToOne(targetEntity="InventoryAlert", inversedBy="logs")
	 * @ORM\JoinColumn(nullable=false)
	 * @JMS\Type("AppBundle\Entity\InventoryAlert")
	 */

	protected $inventoryAlert = null;

	public function getInventoryAlert()
	{
		return $this->inventoryAlert;
	}

	public function setInventoryAlert(InventoryAlert $inventoryAlert)
	{
		$this->inventoryAlert = $inventoryAlert;
		return $this;
	}

	/**
	 * @ORM\Column(type="datetime", nullable=false)
	 * @JMS\Type("DateTime")
	 */

	protected $performedAt = null;

	public function getPerformedAt()
	{
		return $this->performedAt;
	}

	public function setPerformedAt(\DateTime $performedAt)
	{
		$this->performedAt = $performedAt;
		return $this;
	}

	/**
	 * @ORM\Column(type="smallint", nullable=false)
     * @JMS\Type("integer")
     */
	protected $count = null;

	public function getCount()
	{
		return $this->count;
	}

	public function setCount($count)
	{
		$this->count = $count;
		return $this;
	}

	/**
	 * @ORM\Column(type="boolean")
     * @JMS\Type("boolean")
     */
	protected $isActive = null;

	public function getIsActive()
	{
		return $this->isActive;
	}

	public function setIsActive($isActive)
	{
		$this->isActive = $isActive;
		return $this;
	}

    public function isOwnedByOrganization(Organization $organization)
	{
		if(!$this->getInventoryAlert()){
			throw new \Exception("Inventory Alert Log must Have a Inventory Alert");
		}
		return $this->getInventoryAlert()->isOwnedByOrganization($organization);
	}

}