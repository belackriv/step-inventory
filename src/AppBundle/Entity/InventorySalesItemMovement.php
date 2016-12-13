<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation As JMS;

/**
 * @ORM\Entity
 * @ORM\Table()
 */
Class InventorySalesItemMovement extends InventoryMovement
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

	public function isOwnedByOrganization(Organization $organization)
    {
        return (
        	parent::isOwnedByOrganization($organization) and
			$this->getSalesItem() and $this->getSalesItem()->isOwnedByOrganization($organization)
		);
    }

}