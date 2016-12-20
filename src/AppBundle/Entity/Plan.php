<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation As JMS;

/**
 * @ORM\Entity
 * @ORM\Table()
 */
Class Plan
{
    const TRIAL_NAME = 'Trial';

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
     * @ORM\Column(type="string", length=64, unique=true)
     * @JMS\Exclude
     */
    protected $externalId = null;

    public function getExternalId()
    {
        return $this->externalId;
    }

    public function setExternalId($externalId)
    {
        $this->externalId = $externalId;
        return $this;
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
     * @ORM\Column(type="string", length=3)
     * @JMS\Type("string")
     */
    protected $currency = null;

    public function getCurrency()
    {
        return $this->currency;
    }

    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @ORM\Column(type="string", name="plan_interval", length=6)
     * @JMS\Type("string")
     */
    protected $interval = null;

    public function getInterval()
    {
        return $this->interval;
    }

    public function setInterval($interval)
    {
        $this->interval = $interval;
        return $this;
    }

    /**
     * @ORM\Column(type="smallint", nullable=true)
     * @JMS\Type("integer")
     */
    protected $intervalCount = null;

    public function getIntervalCount()
    {
        return $this->intervalCount;
    }

    public function setIntervalCount($intervalCount)
    {
        $this->intervalCount = $intervalCount;
        return $this;
    }

    /**
     * @ORM\Column(type="smallint", nullable=true)
     * @JMS\Type("integer")
     */
    protected $trialPeriodDays = null;

    public function getTrialPeriodDays()
    {
        return $this->trialPeriodDays;
    }

    public function setTrialPeriodDays($trialPeriodDays)
    {
        $this->trialPeriodDays = $trialPeriodDays;
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

    public function updateFromStripe(\Stripe\Plan $stripePlan)
    {
        $this->externalId = $stripePlan->id;
        $this->name = $stripePlan->name;
        $this->description = $stripePlan->statement_descriptor;
        $this->amount = $stripePlan->amount;
        $this->currency = $stripePlan->currency;
        $this->interval = $stripePlan->interval;
        $this->intervalCount = $stripePlan->interval_count;
        $this->trialPeriodDays = $stripePlan->trial_period_days;
        if(isset($stripePlan->metadata['max_concurrent_users'])){
            $this->maxConcurrentUsers = (int)$stripePlan->metadata['max_concurrent_users'];
        }
        if(isset($stripePlan->metadata['max_skus'])){
            $this->maxSkus = (int)$stripePlan->metadata['max_skus'];
        }
    }

}

