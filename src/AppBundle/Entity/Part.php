<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation As JMS;

/**
 * @ORM\Entity
 * @ORM\Table()
 */
Class Part
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
	 * @ORM\Column(type="string", length=64,  nullable=true)
     * @JMS\Type("string")
     */
	protected $partId = null;

	public function getPartId()
	{
		return $this->partId;
	}

	public function setPartId($partId)
	{
		$this->partId = $partId;
		return $this;
	}

	/**
	 * @ORM\Column(type="string", length=64,  nullable=true)
     * @JMS\Type("string")
     */
	protected $partAltId = null;

	public function getPartAltId()
	{
		return $this->partAltId;
	}

	public function setPartAltId($partAltId)
	{
		$this->partAltId = $partAltId;
		return $this;
	}

	/**
	 * @ORM\Column(type="text", nullable=true)
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
     * @ORM\ManyToOne(targetEntity="UploadedImage")
     * @JMS\Type("AppBundle\Entity\UploadedImage")
     */
	protected $image = null;

	public function getImage()
	{
		return $this->image;
	}

	public function setImage(UploadedImage $uploadedImage)
	{
		$this->image = $uploadedImage;
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

	public function setPartCategory(PartCategory $partCategory = null)
	{
		$this->partCategory = $partCategory;
		return $this;
	}

	/**
	 * @ORM\ManyToOne(targetEntity="PartGroup")
	 * @JMS\Type("AppBundle\Entity\PartGroup")
	 */

	protected $partGroup = null;

	public function getPartGroup()
	{
		return $this->partGroup;
	}

	public function setPartGroup(PartGroup $partGroup = null)
	{
		$this->partGroup = $partGroup;
		return $this;
	}

	/**
	 * @ORM\ManyToOne(targetEntity="Organization", inversedBy="parts")
	 * @ORM\JoinColumn(nullable=false)
	 * @JMS\Exclude
	 */
	protected $organization = null;

	public function getOrganization()
	{
		return $this->organization;
	}

	public function setOrganization(Organization $organization)
	{
		$this->organization = $organization;
		return $this;
	}

	/**
	 * @ORM\Column(type="boolean")
     * @JMS\Type("boolean")
     */
	protected $isActive = null;

	public function getIsActive()
	{
		return $this->isActive;
	}

	public function setIsActive($isActive)
	{
		$this->isActive = $isActive;
		return $this;
	}

	public function isOwnedByOrganization(Organization $organization)
    {
        return (
        	(!$this->getPartCategory() or $this->getPartCategory()->isOwnedByOrganization($organization)) and
        	(!$this->getPartGroup() or $this->getPartGroup()->isOwnedByOrganization($organization)) and
        	$this->getOrganization() === $organization
    	);
    }

    public function getSelectOptionData()
	{
		return [
			'id' => $this->id,
			'name' => $this->name,
			'isActive' => $this->isActive
		];
	}

}