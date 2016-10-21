<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation As JMS;

/**
 * @ORM\Entity
 * @ORM\Table()
 */
Class InventoryTravelerIdMovement extends InventoryMovement
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
	 * @ORM\ManyToOne(targetEntity="TravelerId")
	 * @ORM\JoinColumn(nullable=false)
	 * @JMS\Type("AppBundle\Entity\TravelerId")
	 */

	protected $travelerId = null;

	public function getTravelerId()
	{
		return $this->travelerId;
	}

	public function setTravelerId(TravelerId $travelerId)
	{
		$this->travelerId = $travelerId;
		return $this;
	}

	public function isOwnedByOrganization(Organization $organization)
    {
        return (
        	parent::isOwnedByOrganization($organization) and
			$this->getTravelerId() and $this->getTravelerId()->isOwnedByOrganization($organization)
		);
    }

}