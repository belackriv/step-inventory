<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation As JMS;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator", type="string")
 * @ORM\DiscriminatorMap({"AccountSubscriptionChange" = "AccountSubscriptionChange", "AccountOwnerChange" = "AccountOwnerChange"})
 * @JMS\Discriminator(field = "discriminator", map = {
 *      "AccountSubscriptionChange": "AppBundle\Entity\AccountSubscriptionChange",
 *      "AccountOwnerChange": "AppBundle\Entity\AccountOwnerChange"
 *  })
 */
abstract Class AccountChange
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
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(nullable=false)
     * @JMS\Type("AppBundle\Entity\User")
     */

    protected $changedBy = null;

    public function getChangedBy()
    {
        return $this->changedBy;
    }

    public function setChangedBy(User $changedBy)
    {
        $this->changedBy = $changedBy;
        return $this;
    }

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @JMS\Type("DateTime")
     */

    protected $changedAt = null;

    public function getChangedAt()
    {
        return $this->changedAt;
    }

    public function setChangedAt(\DateTime $changedAt)
    {
        $this->changedAt = $changedAt;
        return $this;
    }

    /**
     * @ORM\ManyToOne(targetEntity="Account", inversedBy="accChanges")
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


    public abstract function updateAccount();
}
