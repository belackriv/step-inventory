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
     * @ORM\Column(type="decimal", precision=7, scale=2)
     * @JMS\Type("float")
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
     * @ORM\Column(type="boolean")
     * @JMS\Type("boolean")
     */
    protected $isClosed = null;

    public function getIsClosed()
    {
        return $this->isClosed;
    }

    public function setIsClosed($isClosed)
    {
        $this->isClosed = $isClosed;
        return $this;
    }

    /**
     * @ORM\Column(type="string", length=3)
     * @JMS\Type("string")
     */
    protected $currency = null;

    public function getCurrency()
    {
        return $this->currency;
    }

    public function setCurrency($currency)
    {
        $this->currency = $currency;
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

    public function updateFromStripe(\Stripe\Invoice $stripeInvoice)
    {
        $this->externalId = $stripeInvoice->id;
        $this->amount = $stripeInvoice->amount_due/100;
        $this->isClosed = $stripeInvoice->closed;
        $this->currency = $stripeInvoice->currency;
        $this->chargedAt = new \DateTime("@".$stripeInvoice->date);
    }


 }
