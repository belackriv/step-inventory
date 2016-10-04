<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation As JMS;

/** @ORM\MappedSuperclass */
Class InventoryAdjustment
{

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

	public function isOwnedByOrganization(Organization $organization)
	{
		return (
			$this->getByUser()->isOwnedByOrganization($organization) and
			$this->getForBin()->isOwnedByOrganization($organization)
		);
	}

}