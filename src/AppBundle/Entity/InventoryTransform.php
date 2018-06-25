<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation As JMS;

/** @ORM\MappedSuperclass */
Class InventoryTransform
{

	/**
	 * @ORM\ManyToOne(targetEntity="User")
	 * @ORM\JoinColumn(nullable=false)
	 * @JMS\Type("AppBundle\Entity\User")
	 * @JMS\Groups({"MassUpdate"})
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
	 * @ORM\Column(type="datetime", nullable=false)
	 * @JMS\Type("DateTime")
	 * @JMS\Groups({"MassUpdate"})
	 */

	protected $transformedAt = null;

	public function getTransformedAt()
	{
		return $this->transformedAt;
	}

	public function setTransformedAt(\DateTime $transformedAt)
	{
		$this->transformedAt = $transformedAt;
		return $this;
	}

	/**
	 * @ORM\Column(type="decimal", precision=7, scale=2, nullable=false)
	 * @JMS\Type("float")
	 * @JMS\Groups({"MassUpdate"})
	 */
	protected $quantity;

	public function getQuantity()
	{
		return $this->quantity;
	}

	public function setQuantity($quantity)
	{
		$this->quantity = $quantity;
		return $this;
	}

    /**
	 * @ORM\Column(type="boolean")
     * @JMS\Type("boolean")
     * @JMS\Groups({"Default","OrderManifest","MassUpdate"})
     */
	protected $isVoid = null;

	public function getIsVoid()
	{
		return $this->isVoid;
	}


	/**
	 * For tracking client created transforms
     * @JMS\Type("string")
     * @JMS\Groups({"Default","MassUpdate"})
     */
	public $cid = null;
}