<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation As JMS;

/** @ORM\MappedSuperclass */
Class InventoryMovement
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
	 * @ORM\JoinColumn(nullable=false)
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
	 * @ORM\Column(type="datetime", nullable=false)
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

	/**
	 * @ORM\Column(type="simple_array", nullable=true)
     * @JMS\Type("array")
     */
	protected $tags = [];

	public function getTags()
	{
		return $this->tags;
	}

	public function setTags(array $tags)
	{
		$this->tags = $tags;
		return $this;
	}

	public function addTag($tag)
	{
		if(!in_array((string)$tag, $this->tags)){
			$this->tags[] = (string)$tag;
		}
		return $this;
	}

	public function removeTag($tag)
	{
		$index = array_search((string)$tag, $this->tags, true);
		if($index !== false){
			array_splice($array, $index, 1);
		}
		return $this;
	}

	public function hasTag($tag)
	{
		return in_array((string)$tag, $this->tags);
	}

	public function isOwnedByOrganization(Organization $organization)
	{
		return (
			$this->getByUser()->isOwnedByOrganization($organization) and
			$this->getFromBin()->isOwnedByOrganization($organization) and
			$this->getToBin()->isOwnedByOrganization($organization)
		);
	}

}