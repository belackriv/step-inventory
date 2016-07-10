<?php
namespace AppBundle\Entity;

use Symfony\Component\Security\Core\Role\RoleInterface;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation As JMS;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="stepthrough_role")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\RoleRepository")
 */
class Role implements RoleInterface
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Type("integer")
     */
    protected $id;

    /**
     * @ORM\Column(name="name", type="string", length=30)
     * @JMS\Type("string")
     */
    protected $name;

    /**
     * @ORM\Column(name="role", type="string", length=20, unique=true)
     * @JMS\Type("string")
     */
    protected $role;

    /**
     * @ORM\Column(name="is_allowed_to_switch", type="boolean")
     * @JMS\Type("boolean")
     */
    protected $isAllowedToSwitch = false;

    /**
     * @ORM\OneToMany(targetEntity="RoleRole",  mappedBy="roleSource", cascade={"all"}, orphanRemoval=true)
     * @JMS\Exclude
     */
    protected $roleHierarchy;

    /**
     * @see RoleInterface
     */
    public function getRole()
    {
        return $this->role;
    }

    // ... getters and setters for each property

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
     * Set name
     *
     * @param string $name
     * @return Role
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set role
     *
     * @param string $role
     * @return Role
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Set isAllowedToSwitch
     *
     * @param boolean $isAllowedToSwitch
     * @return Role
     */
    public function setIsAllowedToSwitch($isAllowedToSwitch)
    {
        $this->isAllowedToSwitch = $isAllowedToSwitch;

        return $this;
    }

    /**
     * Get isAllowedToSwitch
     *
     * @return boolean
     */
    public function getIsAllowedToSwitch()
    {
        return $this->isAllowedToSwitch;
    }

    public function hasRoleInHierarchy(Role $role)
    {
        foreach($this->roleHierarchy as $roleRole){
            if($roleRole->getTargetRole() === $role){
                return true;
            }
        }
        return false;
    }

    /**
     * Add roleHierarchy
     *
     * @param \AppBundle\Entity\Role $roleHierarchy
     * @return Role
     */
    public function addRoleToHierarchy(Role $role)
    {
        if(!$this->hasRoleInHierarchy($role)){
            $roleRole = new RoleRole();
            $roleRole->setRoleSource($this);
            $roleRole->setRoleTarget($role);
            $this->roleHierarchy[] = $roleRole;
        }

        return $this;
    }

    /**
     * Remove roleHierarchy
     *
     * @param \AppBundle\Entity\Role $roleHierarchy
     */
    public function removeRoleFromHierarchy(Role $role)
    {
        foreach($this->roleHierarchy as $roleRole){
            if($roleRole->getTargetRole() === $role){
                $roleRole->setRoleSource(null);
                $roleRole->setRoleTarget(null);
                $this->roleHierarchy->removeElement($roleRole);
            }
        }
    }

    /**
     * Get roleHierarchy
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRoleHierarchy()
    {
        return $this->roleHierarchy;
    }

    /**
     * Add roleHierarchy
     *
     * @param \AppBundle\Entity\Role $roleHierarchy
     * @return Role
     */
    public function addRoleHierarchy(Role $roleHierarchy)
    {
        $this->roleHierarchy[] = $roleHierarchy;

        return $this;
    }

    /**
     * Remove roleHierarchy
     *
     * @param \AppBundle\Entity\Role $roleHierarchy
     */
    public function removeRoleHierarchy(Role $roleHierarchy)
    {
        $this->roleHierarchy->removeElement($roleHierarchy);
    }

    public function __construct()
    {
        $this->roleHierarchy = new ArrayCollection();
    }
}
