<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation As JMS;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator", type="string")
 * @ORM\DiscriminatorMap({"PaymentCardSource" = "PaymentCardSource"})
 * @JMS\Discriminator(field = "discriminator", map = {
 *      "PaymentCardSource": "AppBundle\Entity\PaymentCardSource",
 *  })
 */
abstract Class PaymentSource
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
     * @JMS\Type("string")
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
     * @ORM\ManyToOne(targetEntity="Account", inversedBy="paymentSources")
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

    public static function getInstance($stripePaymentSource)
    {
        if(is_a($stripePaymentSource, \Stripe\Source::class)){
            return new PaymentCardSource();
        }
        return null;
    }
}
