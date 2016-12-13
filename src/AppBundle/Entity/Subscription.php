<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation As JMS;

/**
 * @ORM\Entity
 * @ORM\Table()
 */
Class Subscription
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

    /**
     * @ORM\Column(type="smallint", nullable=false)
     * @JMS\Type("integer")
     */
    protected $amount = null;

    public function getAmount()
    {
        return $this->amount;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @ORM\Column(type="smallint", nullable=false)
     * @JMS\Type("integer")
     */
    protected $maxConcurrentUsers = null;

    public function getMaxConcurrentUsers()
    {
        return $this->maxConcurrentUsers;
    }

    public function setMaxConcurrentUsers($maxConcurrentUsers)
    {
        $this->maxConcurrentUsers = $maxConcurrentUsers;
        return $this;
    }

    /**
     * @ORM\Column(type="smallint", nullable=false)
     * @JMS\Type("integer")
     */
    protected $maxSkus = null;

    public function getMaxSkus()
    {
        return $this->maxSkus;
    }

    public function setMaxSkus($maxSkus)
    {
        $this->maxSkus = $maxSkus;
        return $this;
    }

}
