<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation As JMS;

/**
 * @ORM\Entity
 * @ORM\Table()
 */
Class InventoryAdjustment
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
	 * @ORM\Column(type="smallint")
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
	 * @ORM\Column(type="smallint")
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

	/**
	 * @ORM\Column(type="datetime")
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

}