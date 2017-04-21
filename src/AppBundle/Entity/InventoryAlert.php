<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation As JMS;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\InventoryAlertRepository")
 * @ORM\Table()
 */
Class InventoryAlert
{
	const TYPE_LESS_THAN = 1;
	const TYPE_GREATER_THAN = 2;

	/**
	 * @JMS\Type("array")
	 * @JMS\ReadOnly
	 */
	public static $types = [
		self::TYPE_LESS_THAN => 'Less Than',
		self::TYPE_GREATER_THAN => 'Greater Than'
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
	 * @ORM\ManyToOne(targetEntity="Department", inversedBy="inventoryAlerts")
	 * @ORM\JoinColumn(nullable=false)
	 * @JMS\Type("AppBundle\Entity\Department")
	 */

	protected $department = null;

	public function getDepartment()
	{
		return $this->department;
	}

	public function setDepartment(Department $department)
	{
		$this->department = $department;
		return $this;
	}

	/**
	 * @ORM\ManyToOne(targetEntity="Sku")
	 * @ORM\JoinColumn(nullable=false)
	 * @JMS\Type("AppBundle\Entity\Sku")
	 */

	protected $sku = null;

	public function getSku()
	{
		return $this->sku;
	}

	public function setSku(Sku $sku)
	{
		$this->sku = $sku;
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
	protected $count = null;

	public function getCount()
	{
		return $this->count;
	}

	public function setCount($count)
	{
		$this->count = $count;
		return $this;
	}

	/**
	 * @ORM\Column(type="smallint", nullable=false)
     * @JMS\Type("integer")
     */
	protected $type = null;

	public function getType()
	{
		return $this->type;
	}

	public function setType($type)
	{
		$this->type = $type;
		return $this;
	}

	public function getTypeName()
	{
		return self::$types[$this->type];
	}


	public function getUsersEmails()
	{
		$emails = [];
		foreach($this->getDepartment()->getOffice()->getOrganization()->getUsers() as $user){
			if($user->getIsActive() and $user->getReceivesInventoryAlert()){
				$emails[] = $user->getEmail();
			}
		}
		return $emails;
	}

	/**
     * @ORM\OneToMany(targetEntity="InventoryAlertLog", mappedBy="inventoryAlert")
     * @JMS\Type("ArrayCollection<AppBundle\Entity\InventoryAlertLog>")
     * @JMS\Exclude
     */
    protected $logs;

    public function getLogs()
    {
        return $this->logs;
    }

    public function __construct() {
        $this->logs = new ArrayCollection();
    }

    public function isOwnedByOrganization(Organization $organization)
	{
		if(!$this->getDepartment()){
			throw new \Exception("Inventory Alert must Have a Department");
		}
		if(!$this->getSku()){
			throw new \Exception("Inventory Alert must Have a SKU");
		}
		return (
			$this->getDepartment() and $this->getDepartment()->isOwnedByOrganization($organization) and
			$this->getSku() and $this->getSku()->isOwnedByOrganization($organization)
		);
	}

}