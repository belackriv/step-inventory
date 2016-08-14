<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation As JMS;

/**
 * @ORM\Entity
 * @ORM\Table()
 */
Class LabelOnSitePrinterType
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
	 * @ORM\ManyToOne(targetEntity="Label", )
	 * @ORM\JoinColumn(nullable=false)
	 * @JMS\Type("AppBundle\Entity\OnSitePrinterType")
	 */

	protected $label = null;

	public function getLabel()
	{
		return $this->label;
	}

	public function setLabel(Label $label)
	{
		$this->label = $label;
		return $this;
	}

	/**
	 * @ORM\ManyToOne(targetEntity="OnSitePrinterType", )
	 * @ORM\JoinColumn(nullable=false)
	 * @JMS\Type("AppBundle\Entity\OnSitePrinterType")
	 */

	protected $onSitePrinterType = null;

	public function getOnSitePrinterType()
	{
		return $this->onSitePrinterType;
	}

	public function setOnSitePrinterType(OnSitePrinterType $onSitePrinterType)
	{
		$this->onSitePrinterType = $onSitePrinterType;
		return $this;
	}

}