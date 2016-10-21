<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation As JMS;

/**
 * @ORM\Entity
 * @ORM\Table()
 */
Class InventorySkuMovement extends InventoryMovement
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

	public function isOwnedByOrganization(Organization $organization)
	{
		return (
			parent::isOwnedByOrganization($organization) and
			$this->getSku() and $this->getSku()->isOwnedByOrganization($organization)
		);
	}
}