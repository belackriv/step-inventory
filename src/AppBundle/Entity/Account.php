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
     * @ORM\Column(type="string", length=64, unique=true, nullable=true)
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
     * @JMS\Type("string")
     * @JMS\ReadOnly
     */
    public $stripePublicKey;

    /**
     * @JMS\Type("array")
     * @JMS\ReadOnly
     */
    public $currentSessions;

      /**
     * @JMS\Type("integer")
     * @JMS\ReadOnly
     */
    public $monthlyTravelerIds;

    /**
     * @ORM\OneToOne(targetEntity="Organization", inversedBy="account", cascade={"merge"})
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
     * @ORM\OneToOne(targetEntity="User", cascade={"merge"})
     * @ORM\JoinColumn(nullable=false)
     * @JMS\Type("AppBundle\Entity\User")
     * @JMS\ReadOnly
     */

    protected $owner = null;

    public function getOwner()
    {
        return $this->owner;
    }

    public function changeOwner(AccountOwnerChange $ownerChange)
    {
        if( $ownerChange->getNewOwner() === null  ){
            throw new \Exception("Cannot change owner to 'null'.");
        }
        $this->owner = $ownerChange->getNewOwner();
        $this->addAccountChange($ownerChange);
        return $this;
    }

     /**
     * @JMS\VirtualProperty
     */
    public function getOwnerSelections()
    {
        $validOwners = [];
        foreach($this->getOrganization()->getUsers() as $user){
            foreach($user->getUserRoles() as $userRole){
                $role = $userRole->getRole();
                if($role->getRole() === 'ROLE_ADMIN'){
                    $validOwners[] = $user;
                }
            }
        }
        return $validOwners;
    }

    /**
     * @ORM\OneToOne(targetEntity="Subscription")
     * @ORM\JoinColumn(nullable=true)
     * @JMS\Type("AppBundle\Entity\Subscription")
     * @JMS\ReadOnly
     */

    protected $subscription = null;

    public function getSubscription()
    {
        return $this->subscription;
    }

    public function setSubscription(Subscription $subscription = null)
    {
        $this->subscription = $subscription;
        if($subscription and $subscription->getAccount() !== $this){
            $subscription->setAccount($this);
        }
        return $this;
    }

    public function changePlan(AccountPlanChange $planChange)
    {
        if( $planChange->getNewPlan() === null  ){
            throw new \Exception("Cannot change subscription to 'null'.");
        }
        $this->subscription->setPlan($planChange->getNewPlan());
        $this->addAccountChange($planChange);
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

    /**
     * @ORM\OneToMany(targetEntity="PaymentSource", mappedBy="account", orphanRemoval=true)
     * @JMS\Type("ArrayCollection<AppBundle\Entity\PaymentSource>")
     * @JMS\ReadOnly
     */
    protected $paymentSources;

    public function getPaymentSources()
    {
        return $this->paymentSources;
    }

    public function addPaymentSource(PaymentSource $paymentSource)
    {
        if(!$this->paymentSources->contains($paymentSource)){
            $this->paymentSources->add($paymentSource);
        }
        if($paymentSource->getAccount() !== $this){
            $paymentSource->setAccount($this);
        }
        return $this;
    }

    public function removePaymentSource(PaymentSource $paymentSource)
    {
        $this->paymentSources->removeElement($paymentSource);
        $paymentSource->setAccount(null);
        $this->paymentSources = new ArrayCollection(array_values($this->paymentSources->toArray()));
    }

    public function __construct()
    {
        $this->accountChanges = new ArrayCollection();
        $this->bills = new ArrayCollection();
        $this->paymentSources = new ArrayCollection();
    }

    public function isActive()
    {
        if($this->subscription){
            return $this->subscription->isActive();
        }else{
            return false;
        }
    }
}
