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
    const STATUS_TRIALING = 1;
    const STATUS_ACTIVE = 2;
    const STATUS_PAST_DUE = 3;
    const STATUS_CANCELED = 4;
    const STATUS_UNPAID = 5;

    public static $stripeStatuses = [
        'trialing' => self::STATUS_TRIALING,
        'active' => self::STATUS_ACTIVE,
        'past_due' => self::STATUS_PAST_DUE,
        'canceled' => self::STATUS_CANCELED,
        'unpaid' => self::STATUS_UNPAID,
    ];

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
     * @ORM\ManyToOne(targetEntity="Account")
     * @ORM\JoinColumn(nullable=false)
     * @JMS\Type("AppBundle\Entity\Account")
     */

    protected $account = null;

    public function getAccount()
    {
        return $this->account;
    }

    public function setAccount(Account $account = null)
    {
        $this->account = $account;
        return $this;
    }

    /**
     * @ORM\ManyToOne(targetEntity="Plan")
     * @JMS\Type("AppBundle\Entity\Plan")
     */
    protected $plan = null;

    public function getPlan()
    {
        return $this->plan;
    }

    public function setPlan(Plan $plan)
    {
        $this->plan = $plan;
        return $this;
    }

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @JMS\Type("DateTime")
     */
    protected $createdAt = null;

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @ORM\Column(type="boolean", nullable=false)
     * @JMS\Type("boolean")
     */
    protected $cancelAtPeriodEnd = null;

    public function getCancelAtPeriodEnd()
    {
        return $this->cancelAtPeriodEnd;
    }

    public function setCancelAtPeriodEnd($cancelAtPeriodEnd)
    {
        $this->cancelAtPeriodEnd = (boolean)$cancelAtPeriodEnd;
        return $this;
    }

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @JMS\Type("DateTime")
     */
    protected $canceledAt = null;

    public function getCanceledAt()
    {
        return $this->canceledAt;
    }

    public function setCanceledAt(\DateTime $canceledAt)
    {
        $this->canceledAt = $canceledAt;
        return $this;
    }

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @JMS\Type("DateTime")
     */
    protected $currentPeriodEnd = null;

    public function getCurrentPeriodEnd()
    {
        return $this->currentPeriodEnd;
    }

    public function setCurrentPeriodEnd(\DateTime $currentPeriodEnd)
    {
        $this->currentPeriodEnd = $currentPeriodEnd;
        return $this;
    }

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @JMS\Type("DateTime")
     */
    protected $currentPeriodStart = null;

    public function getCurrentPeriodStart()
    {
        return $this->currentPeriodStart;
    }

    public function setCurrentPeriodStart(\DateTime $currentPeriodStart)
    {
        $this->currentPeriodStart = $currentPeriodStart;
        return $this;
    }

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @JMS\Type("DateTime")
     */
    protected $endedAt = null;

    public function getEndedAt()
    {
        return $this->endedAt;
    }

    public function setEndedAt(\DateTime $endedAt)
    {
        $this->endedAt = $endedAt;
        return $this;
    }

    /**
     * @ORM\Column(type="smallint", nullable=false)
     * @JMS\Type("integer")
     */
    protected $quantity = null;

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
        return $this;
    }


    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @JMS\Type("DateTime")
     */
    protected $startAt = null;

    public function getStartAt()
    {
        return $this->startAt;
    }

    public function setStartAt(\DateTime $startAt)
    {
        $this->startAt = $startAt;
        return $this;
    }

    /**
     * @ORM\Column(type="smallint", nullable=false)
     * @JMS\Type("integer")
     */
    protected $status = null;

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }


    /**
     * @ORM\Column(type="decimal", precision=7, scale=4, nullable=true)
     * @JMS\Type("string")
     */
    protected $taxPercent;

    public function getTaxPercent()
    {
        return $this->taxPercent;
    }

    public function setTaxPercent($taxPercent)
    {
        $this->taxPercent = $taxPercent;
        return $this;
    }

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @JMS\Type("DateTime")
     */
    protected $trialEnd = null;

    public function getTrialEnd()
    {
        return $this->trialEnd;
    }

    public function setTrialEnd(\DateTime $trialEnd)
    {
        $this->trialEnd = $trialEnd;
        return $this;
    }

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @JMS\Type("DateTime")
     */
    protected $trialStart = null;

    public function getTrialStart()
    {
        return $this->trialStart;
    }

    public function setTrialStart(\DateTime $trialStart)
    {
        $this->trialStart = $trialStart;
        return $this;
    }

    public function isActive()
    {
        return ($this->status === self::STATUS_TRIALING or $this->status === self::STATUS_ACTIVE);
    }

    public function updateFromStripe(\Stripe\Subscription $stripeSubscription)
    {
        $this->externalId = $stripeSubscription->id;
        $this->createdAt = new \DateTime("@".$stripeSubscription->created);
        $this->cancelAtPeriodEnd = (boolean)$stripeSubscription->cancel_at_period_end;
        $this->canceledAt = $stripeSubscription->canceled_at?new \DateTime("@".$stripeSubscription->canceled_at):null;
        $this->currentPeriodEnd = new \DateTime("@".$stripeSubscription->current_period_end);
        $this->currentPeriodStart = new \DateTime("@".$stripeSubscription->current_period_start);
        $this->endedAt = $stripeSubscription->ended_at?new \DateTime("@".$stripeSubscription->ended_at):null;
        $this->quantity = $stripeSubscription->quantity;
        $this->startAt = new \DateTime("@".$stripeSubscription->start);
        $this->status = self::$stripeStatuses[$stripeSubscription->status];
        $this->taxPercent = $stripeSubscription->tax_percent;
        $this->trialEnd = $stripeSubscription->trial_end?new \DateTime("@".$stripeSubscription->trial_end):null;
        $this->trialStart = $stripeSubscription->trial_start?new \DateTime("@".$stripeSubscription->trial_start):null;
    }



}
