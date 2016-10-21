<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation As JMS;

/**
 * @ORM\Entity
 * @ORM\Table()
 */
Class InventorySkuAdjustment extends InventoryAdjustment
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
	 * @ORM\Column(type="smallint", nullable=true)
     * @JMS\Type("integer")
     */
	protected $oldCount = null;

	public function getOldCount()
	{
		return $this->oldCount;
	}

	public function setOldCount($oldCount)
	{
		$this->oldCount = $oldCount;
		return $this;
	}

	/**
	 * @ORM\Column(type="smallint", nullable=false)
     * @JMS\Type("integer")
     */
	protected $newCount = null;

	public function getNewCount()
	{
		return $this->newCount;
	}

	public function setNewCount($newCount)
	{
		$this->newCount = $newCount;
		return $this;
	}

	public function isOwnedByOrganization(Organization $organization)
	{
		return (
			parent::isOwnedByOrganization($organization) and
			$this->getSku() and $this->getSku()->isOwnedByOrganization($organization)
		);
	}

}