<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation As JMS;

/**
 * @ORM\Entity
 */
Class AccountPlanChange extends AccountChange
{

    /**
     * @ORM\ManyToOne(targetEntity="Plan")
     * @ORM\JoinColumn(nullable=true)
     * @JMS\Type("AppBundle\Entity\Plan")
     */

    protected $oldPlan = null;

    public function getOldPlan()
    {
        return $this->oldPlan;
    }

    public function setOldPlan(Plan $oldPlan)
    {
        $this->oldPlan = $oldPlan;
        return $this;
    }

    /**
     * @ORM\ManyToOne(targetEntity="Plan")
     * @ORM\JoinColumn(nullable=true)
     * @JMS\Type("AppBundle\Entity\Plan")
     */

    protected $newPlan = null;

    public function getNewPlan()
    {
        return $this->newPlan;
    }

    public function setNewPlan(Plan $newPlan)
    {
        $this->newPlan = $newPlan;
        return $this;
    }

    public function updateAccount()
    {
        $stripeSubscription = \Stripe\Subscription::retrieve($this->account->getSubscription()->getExternalId());
        $stripeSubscription->plan = $this->newPlan->getExternalId();
        $stripeSubscription->trial_end = $stripeSubscription->trial_end;
        $stripeSubscription->save();

        $this->account->getSubscription()->updateFromStripe($stripeSubscription);
        $this->account->changePlan($this);
    }

 }
