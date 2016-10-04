<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation As JMS;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="bin_part_count_unique", columns={"bin_id", "part_id"})})
 */
Class BinPartCount
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
	 * @ORM\ManyToOne(targetEntity="Bin", inversedBy="partCounts")
	 * @ORM\JoinColumn(nullable=false)
	 * @JMS\Type("AppBundle\Entity\Bin")
	 */

	protected $bin = null;

	public function getBin()
	{
		return $this->bin;
	}

	public function setBin(Bin $bin)
	{
		$this->bin = $bin;
		return $this;
	}

	/**
	 * @ORM\ManyToOne(targetEntity="Part")
	 * @ORM\JoinColumn(nullable=false)
	 * @JMS\Type("AppBundle\Entity\Part")
	 */

	protected $part = null;

	public function getPart()
	{
		return $this->part;
	}

	public function setPart(Part $part)
	{
		$this->part = $part;
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
			$this->getBin()->isOwnedByOrganization($organization) and
			$this->getPart()->isOwnedByOrganization($organization)
		);
	}
}