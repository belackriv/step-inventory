<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation As JMS;

/**
 * @ORM\Entity
 * @ORM\Table()
 */
Class TravelerId
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
	protected $travelerId = null;

	public function getTravelerId()
	{
		return $this->travelerId;
	}

	public function setTravelerId($travelerId)
	{
		$this->travelerId = $travelerId;
		return $this;
	}

}
