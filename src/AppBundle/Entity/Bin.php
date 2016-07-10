<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation As JMS;

/**
 * @ORM\Entity
 * @ORM\Table()
 */
Class Bin
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
	 * @ORM\Column(type="string", length=64)
     * @JMS\Type("string")
     */
	protected $name = null;

	public function getName()
	{
		return $this->name;
	}

	public function setName($name)
	{
		$this->name = $name;
		return $this;
	}

	/**
	 * @ORM\Column(type="text")
     * @JMS\Type("string")
     */
	protected $description = null;

	public function getDescription()
	{
		return $this->description;
	}

	public function setDescription($description)
	{
		$this->description = $description;
		return $this;
	}

	/**
	 * @ORM\ManyToOne(targetEntity="PartCategory")
	 * @JMS\Type("AppBundle\Entity\PartCategory")
	 */

	protected $partCategory = null;

	public function getPartCategory()
	{
		return $this->partCategory;
	}

	public function setPartCategory(PartCategory $partCategory)
	{
		$this->partCategory = $partCategory;
		return $this;
	}

	/**
	 * @ORM\ManyToOne(targetEntity="BinType")
	 * @JMS\Type("AppBundle\Entity\BinType")
	 */

	protected $binType = null;

	public function getBinType()
	{
		return $this->binType;
	}

	public function setBinType(BinType $binType)
	{
		$this->binType = $binType;
		return $this;
	}

	/**
	 * @ORM\ManyToOne(targetEntity="Bin")
	 * @JMS\Type("AppBundle\Entity\Bin")
	 */

	protected $parent = null;

	public function getParent()
	{
		return $this->parent;
	}

	public function setParent(Bin $bin)
	{
		$this->parent = $bin;
		return $this;
	}

}