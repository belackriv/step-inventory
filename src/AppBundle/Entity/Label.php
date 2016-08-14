<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation As JMS;

/**
 * @ORM\Entity
 * @ORM\Table()
 */
Class Label
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
	 * @ORM\Column(type="string", length=64)
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
	 * @ORM\Column(type="text")
     * @JMS\Type("string")
     */
	protected $description = null;

	public function getDescription()
	{
		return $this->description;
	}

	public function setDescription($description)
	{
		$this->description = $description;
		return $this;
	}

	/**
	 * @ORM\Column(type="text")
     * @JMS\Type("string")
     */
	protected $template = null;

	public function getTemplate()
	{
		return $this->template;
	}

	public function setTemplate($template)
	{
		$this->template = $template;
		return $this;
	}

	/**
     * @ORM\OneToMany(targetEntity="LabelOnSitePrinterType", mappedBy="label", cascade={"persist"})
     * @JMS\Type("ArrayCollection<AppBundle\Entity\LabelOnSitePrinterType>")
     */
    protected $labelOnSitePrinterTypes;

	/**
     * Add labelOnSitePrinterTypes
     *
     * @param \AppBundle\Entity\LabelOnSitePrinterType $labelOnSitePrinterTypes
     * @return Label
     */
    public function addLabelOnSitePrinterType(LabelOnSitePrinterType $labelOnSitePrinterType)
    {
        $this->labelOnSitePrinterTypes[] = $labelOnSitePrinterType;

        return $this;
    }

    /**
     * Remove labelOnSitePrinterTypes
     *
     * @param \AppBundle\Entity\LabelOnSitePrinterType $labelOnSitePrinterTypes
     */
    public function removeLabelOnSitePrinterType(LabelOnSitePrinterType $labelOnSitePrinterType)
    {
        $this->labelOnSitePrinterTypes->removeElement($labelOnSitePrinterType);
    }

    public function getLabelOnSitePrinterTypes(){
        return $this->labelOnSitePrinterTypes;
    }

    public function __construct()
    {
        $this->labelOnSitePrinterTypes = new ArrayCollection();
    }

}