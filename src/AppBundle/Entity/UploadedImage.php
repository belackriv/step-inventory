<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation As JMS;

/**
 * @ORM\Entity
 * @ORM\Table()
 */
Class UploadedImage
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
	 * @ORM\Column(type="string", length=32)
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
     * @ORM\Column(type="string", length=32)
     * @JMS\Type("string")
     */
    protected $mimeType = null;

    public function getMimeType()
    {
        return $this->mimeType;
    }

    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;
        return $this;
    }

    /**
     * @ORM\ManyToOne(targetEntity="Organization")
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
     * @ORM\Column(type="blob", nullable=true)
     * @JMS\Exclude
     */
    protected $data = null;

    public function getData()
    {
        return $this->data;
    }

    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    public function isOwnedByOrganization(Organization $organization)
    {
        return ( $this->getOrganization() === $organization );
    }
}
