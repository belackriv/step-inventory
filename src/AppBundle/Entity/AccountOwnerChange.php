<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation As JMS;

/**
 * @ORM\Entity
 */
Class AccountOwnerChange extends AccountChange
{

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(nullable=true)
     * @JMS\Type("AppBundle\Entity\User")
     */

    protected $oldOwner = null;

    public function getOldOwner()
    {
        return $this->oldOwner;
    }

    public function setOldOwner(User $oldOwner)
    {
        $this->oldOwner = $oldOwner;
        return $this;
    }

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(nullable=true)
     * @JMS\Type("AppBundle\Entity\User")
     */

    protected $newOwner = null;

    public function getNewOwner()
    {
        return $this->newOwner;
    }

    public function setNewOwner(User $newOwner)
    {
        $this->newOwner = $newOwner;
        return $this;
    }

    public function updateAccount()
    {
        $this->account->changeOwner($this);
    }

 }
