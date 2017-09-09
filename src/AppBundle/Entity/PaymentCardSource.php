<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation As JMS;

/**
 * @ORM\Entity
 */
Class PaymentCardSource extends PaymentSource
{
    /**
     * @ORM\Column(type="string", length=64)
     * @JMS\ReadOnly
     */
    protected $brand = null;

    public function getBrand()
    {
        return $this->brand;
    }

    public function setBrand($brand)
    {
        $this->brand = $brand;
        return $this;
    }

    /**
     * @ORM\Column(type="string", length=4)
     * @JMS\ReadOnly
     */
    protected $last4 = null;

    public function getLast4()
    {
        return $this->last4;
    }

    public function setLast4($last4)
    {
        $this->last4 = $last4;
        return $this;
    }

   /**
     * @ORM\Column(type="string", length=2)
     * @JMS\ReadOnly
     */
    protected $expirationMonth = null;

    public function getExpirationMonth()
    {
        return $this->expirationMonth;
    }

    public function setExpirationMonth($expirationMonth)
    {
        $this->expirationMonth = $expirationMonth;
        return $this;
    }

    /**
     * @ORM\Column(type="string", length=4)
     * @JMS\ReadOnly
     */
    protected $expirationYear = null;

    public function getExpirationYear()
    {
        return $this->expirationYear;
    }

    public function setExpirationYear($expirationYear)
    {
        $this->expirationYear = $expirationYear;
        return $this;
    }
/*
    "address_city": null,
    "address_country": null,
    "address_line1": null,
    "address_line1_check": null,
    "address_line2": null,
    "address_state": null,
    "address_zip": null,
    "address_zip_check": null,
    "brand": "Visa",
    "country": "US",
    "customer": null,
    "cvc_check": "unchecked",
    "exp_month": 5,
    "exp_year": 2017,
    "funding": "credit",
    "last4": "4242",
    "name": null,
    "tokenization_method": null
*/
    public function updateFromStripe(\Stripe\Source $stripeSource)
    {
        $this->externalId = $stripeSource->id;
        $this->brand = $stripeSource->card->brand;
        $this->last4 = $stripeSource->card->last4;
        $this->expirationMonth = $stripeSource->card->exp_month;
        $this->expirationYear = $stripeSource->card->exp_year;
    }



}
