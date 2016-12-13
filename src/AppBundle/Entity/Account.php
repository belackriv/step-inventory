<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation As JMS;

/**
 * @ORM\Entity
 * @ORM\Table()
 */
Class Account
{
	/**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Type("integer")
     * @JMS\ReadOnly
     */
	protected $id = null;

	public function getId()
	{
		return $this->id;
	}

    /**
     * @ORM\OneToOne(targetEntity="Organization", inversedBy="account")
     * @ORM\JoinColumn(nullable=false)
     * @JMS\Type("AppBundle\Entity\Organization")
     * @JMS\ReadOnly
     */

    protected $organization = null;

    public function getOrganization()
    {
        return $this->organization;
    }

    public function setOrganization(Organization $organization)
    {
        $this->organization = $organization;
        if($organization->getAccount() !== $this){
            $organization->setAccount($this);
        }
        return $this;
    }


    /**
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn(nullable=false)
     * @JMS\Type("AppBundle\Entity\User")
     * @JMS\ReadOnly
     */

    protected $owner = null;

    public function getOwner()
    {
        return $this->owner;
    }

    public function changeOwner(AccountOwnerhange $ownerChange)
    {
        if( $ownerChange->getNewOwner() === null  ){
            throw new Exception("Cannot change owner to 'null'.");
        }
        $this->owner = $ownerChange->getNewOwner();
        $this->addAccountChange($ownerChange);
        return $this;
    }

    /**
     * @ORM\ManyToOne(targetEntity="Subscription")
     * @ORM\JoinColumn(nullable=false)
     * @JMS\Type("AppBundle\Entity\Subscription")
     * @JMS\ReadOnly
     */

    protected $subscription = null;

    public function getSubscription()
    {
        return $this->subscription;
    }

    public function changeSubscription(AccountSubscriptionChange $subscriptionChange)
    {
        if( $subscriptionChange->getNewSubscription() === null  ){
            throw new Exception("Cannot change subscription to 'null'.");
        }
        $this->subscription = $subscriptionChange->getNewSubscription();
        $this->addAccountChange($subscriptionChange);
        return $this;
    }


    /**
     * @ORM\OneToMany(targetEntity="AccountChange", mappedBy="account", orphanRemoval=true)
     * @JMS\Type("ArrayCollection<AppBundle\Entity\AccountChange>")
     * @JMS\ReadOnly
     */
    protected $accountChanges;

    public function getAccountChanges()
    {
        return $this->accountChanges;
    }

    public function addAccountChange(AccountChange $accountChange)
    {
        if(!$this->accountChanges->contains($accountChange)){
            $this->accountChanges->add($accountChange);
        }
        if($accountChange->getAccount() !== $this){
            $accountChange->setAccount($this);
        }
        return $this;
    }

    public function removeAccountChange(AccountChange $accountChange)
    {
        $this->accountChanges->removeElement($accountChange);
        $accountChange->setAccount(null);
        $this->accountChanges = new ArrayCollection(array_values($this->accountChanges->toArray()));
    }

    /**
     * @ORM\OneToMany(targetEntity="Bill", mappedBy="account", orphanRemoval=true)
     * @JMS\Type("ArrayCollection<AppBundle\Entity\Bill>")
     * @JMS\ReadOnly
     */
    protected $bills;

    public function getBills()
    {
        return $this->bills;
    }

    public function addBill(Bill $bill)
    {
        if(!$this->bills->contains($bill)){
            $this->bills->add($bill);
        }
        if($bill->getAccount() !== $this){
            $bill->setAccount($this);
        }
        return $this;
    }

    public function removeBill(Bill $bill)
    {
        $this->bills->removeElement($bill);
        $bill->setAccount(null);
        $this->bills = new ArrayCollection(array_values($this->bills->toArray()));
    }

}
