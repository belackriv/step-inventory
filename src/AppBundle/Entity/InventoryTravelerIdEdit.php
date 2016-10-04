<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation As JMS;

/**
 * @ORM\Entity
 * @ORM\Table()
 */
Class InventoryTravelerIdEdit
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
	 * @ORM\Column(type="json_array", nullable=false)
	 * @JMS\Type("array")
	 */

	protected $oldAttributes = null;

	public function getOldAttributes()
	{
		return $this->oldAttributes;
	}

	public function setOldAttributes(array $oldAttributes)
	{
		$this->oldAttributes = $oldAttributes;
		return $this;
	}

	/**
	 * @ORM\Column(type="json_array", nullable=false)
	 * @JMS\Type("array")
	 */

	protected $newAttributes = null;

	public function getNewAttributes()
	{
		return $this->newAttributes;
	}

	public function setNewAttributes(array $newAttributes)
	{
		$this->newAttributes = $newAttributes;
		return $this;
	}

	/**
	 * @ORM\Column(type="datetime", nullable=false)
	 * @JMS\Type("DateTime")
	 */

	protected $editedAt = null;

	public function getEditedAt()
	{
		return $this->editedAt;
	}

	public function setEditedAt(\DateTime $editedAt)
	{
		$this->editedAt = $editedAt;
		return $this;
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
        	$this->getByUser()->isOwnedByOrganization($organization) and
			$this->getTravelerId()->isOwnedByOrganization($organization)
		);
    }

}