<?php
namespace AppBundle\Entity;

use Symfony\Component\Security\Core\Role\RoleInterface;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation As JMS;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="role_role")
 * @ORM\Entity
 */
class RoleRole
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Type("integer")
     */
    protected $id;


    /**
     * @ORM\ManyToOne(targetEntity="Role", inversedBy="roleHierarchy")
     * @JMS\Type("AppBundle\Entity\Role")
     */
    protected $roleSource;

    /**
     * @ORM\ManyToOne(targetEntity="Role")
     * @JMS\Type("AppBundle\Entity\Role")
     */
    protected $roleTarget;

    /**
     * Set roleSource
     *
     * @param \AppBundle\Entity\Role $roleSource
     * @return Role
     */
    public function setRoleSource(Role $roleSource)
    {
        $this->roleSource = $roleSource;

        return $this;
    }

    /**
     * Get roleSource
     *
     * @return \AppBundle\Entity\Role
     */
    public function getRoleSource()
    {
        return $this->roleSource;
    }

    /**
     * Set roleTarget
     *
     * @param \AppBundle\Entity\Role $roleTarget
     * @return Role
     */
    public function setRoleTarget(Role $roleTarget)
    {
        $this->roleTarget = $roleTarget;

        return $this;
    }

    /**
     * Get roleTarget
     *
     * @return \AppBundle\Entity\Role
     */
    public function getRoleTarget()
    {
        return $this->roleTarget;
    }
}
