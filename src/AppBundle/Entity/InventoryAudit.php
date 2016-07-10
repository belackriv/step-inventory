<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation As JMS;

/**
 * @ORM\Entity
 * @ORM\Table()
 */
Class InventoryAudit
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
	protected $userCount = null;

	public function getUserCount()
	{
		return $this->userCount;
	}

	public function setUserCount($userCount)
	{
		$this->userCount = $userCount;
		return $this;
	}

	/**
	 * @ORM\Column(type="smallint")
     * @JMS\Type("integer")
     */
	protected $systemCount = null;

	public function getSystemCount()
	{
		return $this->systemCount;
	}

	public function setSystemCount($systemCount)
	{
		$this->systemCount = $systemCount;
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