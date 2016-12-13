<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation As JMS;

/**
 * @ORM\Entity
 */
Class AccountSubscriptionChange extends AccountChange
{

    /**
     * @ORM\ManyToOne(targetEntity="Subscription")
     * @ORM\JoinColumn(nullable=true)
     * @JMS\Type("AppBundle\Entity\Subscription")
     */

    protected $oldSubscription = null;

    public function getOldSubscription()
    {
        return $this->oldSubscription;
    }

    public function setOldSubscription(Subscription $oldSubscription)
    {
        $this->oldSubscription = $oldSubscription;
        return $this;
    }

    /**
     * @ORM\ManyToOne(targetEntity="Subscription")
     * @ORM\JoinColumn(nullable=true)
     * @JMS\Type("AppBundle\Entity\Subscription")
     */

    protected $newSubscription = null;

    public function getNewSubscription()
    {
        return $this->newSubscription;
    }

    public function setNewSubscription(Subscription $newSubscription)
    {
        $this->newSubscription = $newSubscription;
        return $this;
    }

    public function updateAccount()
    {
        $this->account->changeSubscription($this);
    }

 }
