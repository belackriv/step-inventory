<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation As JMS;

/**
 * @ORM\Entity
 * @ORM\Table()
 */
Class Organization
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
     * @ORM\OneToOne(targetEntity="Account", mappedBy="organization")
     * @JMS\Type("AppBundle\Entity\Account")
     */

    protected $account = null;

    public function getAccount()
    {
        return $this->account;
    }

    public function setAccount(Account $account)
    {
        $this->account = $account;
        if($account->getOrganization() !== $this){
            $account->setOrganization($this);
        }
        return $this;
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
     * @ORM\ManyToOne(targetEntity="UploadedImage")
     * @JMS\Type("AppBundle\Entity\UploadedImage")
     */
	protected $logo = null;

	public function getLogo()
	{
		return $this->logo;
	}

	public function setLogo(UploadedImage $uploadedImage)
	{
		$this->logo = $uploadedImage;
		return $this;
	}

	/**
     * @ORM\OneToMany(targetEntity="Client", mappedBy="organization")
     * @JMS\Type("ArrayCollection<AppBundle\Entity\Client>")
     * @JMS\Groups({"Client"})
     * @JMS\ReadOnly
     */
    protected $clients;

    public function getClients()
    {
    	return $this->clients;
    }

    public function addClient(Client $client)
    {
        if(!$this->clients->contains($client)){
            $this->clients->add($client);
        }
        if($client->getOrganization() !== $this){
        	$client->setOrganization($this);
        }
        return $this;
    }

    public function removeClient(Client $client)
    {
        $this->clients->removeElement($client);
        $client->setOrganization(null);
        $this->clients = new ArrayCollection(array_values($this->clients->toArray()));
    }

    /**
     * @ORM\OneToMany(targetEntity="Customer", mappedBy="organization")
     * @JMS\Type("ArrayCollection<AppBundle\Entity\Customer>")
     * @JMS\Groups({"Customer"})
     * @JMS\ReadOnly
     */
    protected $customers;

    public function getCustomers()
    {
    	return $this->customers;
    }

    public function addCustomer(Customer $customer)
    {
        if(!$this->customers->contains($customer)){
            $this->customers->add($customer);
        }
        if($customer->getOrganization() !== $this){
        	$customer->setOrganization($this);
        }
        return $this;
    }

    public function removeCustomer(Customer $customer)
    {
        $this->customers->removeElement($customer);
        $customer->setOrganization(null);
        $this->customers = new ArrayCollection(array_values($this->customers->toArray()));
    }

    /**
     * @ORM\OneToMany(targetEntity="Office", mappedBy="organization")
     * @JMS\Type("ArrayCollection<AppBundle\Entity\Office>")
     * @JMS\Groups({"Office"})
     * @JMS\ReadOnly
     */
    protected $offices;

    public function getOffices()
    {
    	return $this->offices;
    }

    public function addOffice(Office $office)
    {
        if(!$this->offices->contains($office)){
            $this->offices->add($office);
        }
        if($office->getOrganization() !== $this){
        	$office->setOrganization($this);
        }
        return $this;
    }

    public function removeOffice(Office $office)
    {
        $this->offices->removeElement($office);
        $office->setOrganization(null);
        $this->offices = new ArrayCollection(array_values($this->offices->toArray()));
    }

    /**
     * @ORM\OneToMany(targetEntity="BinType", mappedBy="organization")
     * @JMS\Type("ArrayCollection<AppBundle\Entity\BinType>")
     * @JMS\Groups({"BinType"})
     * @JMS\ReadOnly
     */
    protected $binTypes;

    public function getBinTypes()
    {
    	return $this->binTypes;
    }

    public function addBinType(BinType $binType)
    {
        if(!$this->binTypes->contains($binType)){
            $this->binTypes->add($binType);
        }
        if($binType->getOrganization() !== $this){
        	$binType->setOrganization($this);
        }
        return $this;
    }

    public function removeBinType(BinType $binType)
    {
        $this->binTypes->removeElement($binType);
        $binType->setOrganization(null);
        $this->binTypes = new ArrayCollection(array_values($this->binTypes->toArray()));
    }

    /**
     * @ORM\OneToMany(targetEntity="Part", mappedBy="organization")
     * @JMS\Type("ArrayCollection<AppBundle\Entity\Part>")
     * @JMS\Groups({"Part"})
     * @JMS\ReadOnly
     */
    protected $parts;

    public function getParts()
    {
    	return $this->parts;
    }

    public function addPart(Part $part)
    {
        if(!$this->parts->contains($part)){
            $this->parts->add($part);
        }
        if($part->getOrganization() !== $this){
        	$part->setOrganization($this);
        }
        return $this;
    }

    public function removePart(Part $part)
    {
        $this->parts->removeElement($part);
        $part->setOrganization(null);
        $this->parts = new ArrayCollection(array_values($this->parts->toArray()));
    }

    /**
     * @ORM\OneToMany(targetEntity="PartCategory", mappedBy="organization")
     * @JMS\Type("ArrayCollection<AppBundle\Entity\PartCategory>")
     * @JMS\Groups({"PartCategory"})
     * @JMS\ReadOnly
     */
    protected $partCategories;

    public function getPartCategorys()
    {
    	return $this->partCategories;
    }

    public function getPartCategories()
    {
    	return $this->partCategories;
    }

    public function addPartCategory(PartCategory $partCategory)
    {
        if(!$this->partCategories->contains($partCategory)){
            $this->partCategories->add($partCategory);
        }
        if($partCategory->getOrganization() !== $this){
        	$partCategory->setOrganization($this);
        }
        return $this;
    }

    public function removePartCategory(PartCategory $partCategory)
    {
        $this->partCategories->removeElement($partCategory);
        $partCategory->setOrganization(null);
        $this->partCategories = new ArrayCollection(array_values($this->partCategories->toArray()));
    }

    /**
     * @ORM\OneToMany(targetEntity="PartGroup", mappedBy="organization")
     * @JMS\Type("ArrayCollection<AppBundle\Entity\PartGroup>")
     * @JMS\Groups({"PartGroup"})
     * @JMS\ReadOnly
     */
    protected $partGroups;

    public function getPartGroups()
    {
    	return $this->partGroups;
    }

    public function addPartGroup(PartGroup $partGroup)
    {
        if(!$this->partGroups->contains($partGroup)){
            $this->partGroups->add($partGroup);
        }
        if($partGroup->getOrganization() !== $this){
        	$partGroup->setOrganization($this);
        }
        return $this;
    }

    public function removePartGroup(PartGroup $partGroup)
    {
        $this->partGroups->removeElement($partGroup);
        $partGroup->setOrganization(null);
        $this->partGroups = new ArrayCollection(array_values($this->partGroups->toArray()));
    }

    /**
     * @ORM\OneToMany(targetEntity="Sku", mappedBy="organization")
     * @JMS\Type("ArrayCollection<AppBundle\Entity\Sku>")
     * @JMS\Groups({"Sku"})
     * @JMS\ReadOnly
     */
    protected $skus;

    public function getSkus()
    {
        return $this->skus;
    }

    public function addSku(Sku $sku)
    {
        if(!$this->skus->contains($sku)){
            $this->skus->add($sku);
        }
        if($sku->getOrganization() !== $this){
            $sku->setOrganization($this);
        }
        return $this;
    }

    public function removeSku(Sku $sku)
    {
        $this->skus->removeElement($sku);
        $this->skus = new ArrayCollection(array_values($this->skus->toArray()));
    }

    /**
     * @ORM\OneToMany(targetEntity="User", mappedBy="organization")
     * @JMS\Type("ArrayCollection<AppBundle\Entity\User>")
     * @JMS\Groups({"User"})
     * @JMS\ReadOnly
     */
    protected $users;

    public function getUsers()
    {
    	return $this->users;
    }

    public function addUser(User $user)
    {
        if(!$this->users->contains($user)){
            $this->users->add($user);
        }
        if($user->getOrganization() !== $this){
        	$user->setOrganization($this);
        }
        return $this;
    }

    public function removeUser(User $user)
    {
        $this->users->removeElement($user);
        $user->setOrganization(null);
        $this->users = new ArrayCollection(array_values($this->users->toArray()));
    }

    /**
     * @ORM\OneToMany(targetEntity="OnSitePrinter", mappedBy="organization")
     * @JMS\Type("ArrayCollection<AppBundle\Entity\OnSitePrinter>")
     * @JMS\Groups({"OnSitePrinter"})
     * @JMS\ReadOnly
     */
    protected $onSitePrinters;

    public function getOnSitePrinters()
    {
    	return $this->onSitePrinters;
    }

    public function addOnSitePrinter(OnSitePrinter $onSitePrinter)
    {
        if(!$this->onSitePrinters->contains($onSitePrinter)){
            $this->onSitePrinters->add($onSitePrinter);
        }
        if($onSitePrinter->getOrganization() !== $this){
        	$onSitePrinter->setOrganization($this);
        }
        return $this;
    }

    public function removeOnSitePrinter(OnSitePrinter $onSitePrinter)
    {
        $this->onSitePrinters->removeElement($onSitePrinter);
        $onSitePrinter->setOrganization(null);
        $this->onSitePrinters = new ArrayCollection(array_values($this->onSitePrinters->toArray()));
    }

    public function __construct()
    {
        $this->isActive = true;
        $this->clients = new ArrayCollection();
        $this->customers = new ArrayCollection();
        $this->offices = new ArrayCollection();
        $this->binTypes = new ArrayCollection();
        $this->parts = new ArrayCollection();
        $this->partCategories = new ArrayCollection();
        $this->partGroups = new ArrayCollection();
        $this->skus = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->onSitePrinters = new ArrayCollection();
    }

    public function __toString(){
        return '#'.$this->id .' - ' .$this->name;
    }

    public function getUserLimit()
    {
        return $this->getAccount()->getSubscription()->getPlan()->getMaxConcurrentUsers();
    }

}
