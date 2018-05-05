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
        try{
            $stripeSubscription = \Stripe\Subscription::retrieve($this->account->getSubscription()->getExternalId());
        }catch(\Stripe\Error\InvalidRequest $e){
            if(strpos($e->getMessage(), 'No such subscription') !== false){
                $stripeSubscription = \Stripe\Subscription::create([
                  'customer' => $this->account->getExternalId(),
                  'plan' => $this->newPlan->getExternalId()
                ]);
            }else{
                throw $e;
            }
        }
        $stripeSubscription->plan = $this->newPlan->getExternalId();
        $stripeSubscription->trial_end = $stripeSubscription->trial_end;

        try{
            $stripeSubscription->save();
        }catch(\Stripe\Error\InvalidRequest $e){
            if(strpos($e->getMessage(), 'No such subscription') !== false){
                $stripeSubscription = \Stripe\Subscription::create([
                  'customer' => $this->account->getExternalId(),
                  'plan' => $this->newPlan->getExternalId()
                ]);
                $stripeSubscription->save();
            }else{
                throw $e;
            }
        }


        $this->account->getSubscription()->updateFromStripe($stripeSubscription);
        $this->account->changePlan($this);
    }

 }
