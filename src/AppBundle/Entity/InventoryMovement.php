<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation As JMS;

/**
 * @ORM\Entity
 * @ORM\Table()
 */
Class InventoryMovement
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

	protected $fromBin = null;

	public function getFromBin()
	{
		return $this->fromBin;
	}

	public function setFromBin(Bin $bin)
	{
		$this->fromBin = $bin;
		return $this;
	}

	/**
	 * @ORM\ManyToOne(targetEntity="Bin")
	 * @JMS\Type("AppBundle\Entity\Bin")
	 */

	protected $toBin = null;

	public function getToBin()
	{
		return $this->toBin;
	}

	public function setToBin(Bin $bin)
	{
		$this->toBin = $bin;
		return $this;
	}

	/**
	 * @ORM\Column(type="datetime")
	 * @JMS\Type("DateTime")
	 */

	protected $movedAt = null;

	public function getMovedAt()
	{
		return $this->movedAt;
	}

	public function setMovedAt(\DateTime $movedAt)
	{
		$this->movedAt = $movedAt;
		return $this;
	}

}