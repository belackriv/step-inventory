<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation As JMS;

/**
 * @ORM\Entity
 * @ORM\Table()
 */
Class Bill
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
     * @ORM\Column(type="datetime", nullable=false)
     * @JMS\Type("DateTime")
     */

    protected $chargedAt = null;

    public function getChargedAt()
    {
        return $this->chargedAt;
    }

    public function setChargedAt(\DateTime $chargedAt)
    {
        $this->chargedAt = $chargedAt;
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
     * @ORM\ManyToOne(targetEntity="Account", inversedBy="bills")
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

 }
