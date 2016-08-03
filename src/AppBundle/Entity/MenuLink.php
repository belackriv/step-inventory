<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation As JMS;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table()
 */
Class MenuLink
{

	/**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Type("integer")
     * @JMS\Groups({"Default","MenuItem"})
     */

	protected $id = null;

	public function getId()
	{
		return $this->id;
	}

	/**
	 * @ORM\Column(type="string", length=32)
     * @JMS\Type("string")
     * @JMS\Groups({"Default","MenuItem"})
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
	 * @ORM\Column(type="string", length=255, nullable=true)
     * @JMS\Type("string")
     * @JMS\Groups({"Default","MenuItem"})
     */

	protected $url = null;

	public function getUrl()
	{
		return $this->url;
	}

	public function setUrl($url)
	{
		$this->url = $url;
		return $this;
	}

	/**
	 * @ORM\Column(type="simple_array", nullable=true)
     * @JMS\Type("array")
     * @JMS\Groups({"Default","MenuItem"})
     */

	protected $routeMatches = null;

	public function getRouteMatches()
	{
		return $this->routeMatches;
	}

	public function setRouteMatches($routeMatches)
	{
		$this->routeMatches = $routeMatches;
		return $this;
	}

}
