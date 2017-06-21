<?php

namespace AppBundle\Entity;

use AppBundle\Library\Utilities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation As JMS;


Class MassImport
{
    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

	/**
     * @JMS\Type("ArrayCollection<stdClass>")
     * @JMS\Groups({"Default"})
     */
    protected $items;

    public function getItems()
    {
    	return $this->items;
    }

    public function addItem($item)
    {
        if(!$this->items->contains($item)){
            $this->items->add($item);
        }
        return $this;
    }

    public function removeItem($item)
    {
        $this->items->removeElement($item);
        $this->items = new ArrayCollection(array_values($this->items->toArray()));
    }

    /**
     * @JMS\Type("string")
     */
    public $type;

}
