<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation As JMS;

/**
 * @ORM\Entity
 * @ORM\Table()
 */
class SingleQueryReportRole
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Type("integer")
     */
    protected $id;


    /**
     * @ORM\ManyToOne(targetEntity="SingleQueryReport", inversedBy="singleQueryReportRoles")
     * @JMS\Type("AppBundle\Entity\SingleQueryReport")
     */
    protected $singleQueryReport;

    /**
     * @ORM\ManyToOne(targetEntity="Role")
     * @JMS\Type("AppBundle\Entity\Role")
     */
    protected $role;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set singleQueryReport
     *
     * @param \AppBundle\Entity\User $singleQueryReport
     * @return SingleQueryReportRole
     */
    public function setSingleQueryReport(SingleQueryReport $singleQueryReport)
    {
        $this->singleQueryReport = $singleQueryReport;

        return $this;
    }

    /**
     * Get singleQueryReport
     *
     * @return \AppBundle\Entity\SingleQueryReport
     */
    public function getSingleQueryReport()
    {
        return $this->singleQueryReport;
    }

    /**
     * Set role
     *
     * @param \AppBundle\Entity\Role $role
     * @return SingleQueryReportRole
     */
    public function setRole(Role $role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return \AppBundle\Entity\Role
     */
    public function getRole()
    {
        return $this->role;
    }

}
