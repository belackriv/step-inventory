<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation As JMS;

/**
 * AppBundle\Entity\User
 *
 * @ORM\Table(name="step_inventory_user")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\UserRepository")
 */
class User implements AdvancedUserInterface, \Serializable
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Type("integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=25, unique=true)
     * @JMS\Type("string")
     */
    protected $username;

    /**
     * @ORM\Column(type="string", length=64)
     * @JMS\Exclude
     */
    protected $password;

    /**
     * @ORM\Column(type="string", length=60, unique=true)
     * @JMS\Type("string")
     */
    protected $email;

    /**
     * @ORM\Column(type="string", length=64)
     * @JMS\Type("string")
     */
    protected $firstName;

    /**
     * @ORM\Column(type="string", length=64)
     * @JMS\Type("string")
     */
    protected $lastName;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     * @JMS\Type("boolean")
     */
    protected $isActive;

    /**
     * @ORM\ManyToOne(targetEntity="Organization", inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     * @JMS\Type("AppBundle\Entity\Organization")
     */
    protected $organization;

    /**
     * @ORM\ManyToOne(targetEntity="Department")
     * @JMS\Type("AppBundle\Entity\Department")
     */
    protected $defaultDepartment;

    /**
     * @ORM\OneToMany(targetEntity="UserRole", mappedBy="user", cascade={"persist"})
     * @JMS\Type("ArrayCollection<AppBundle\Entity\UserRole>")
     */
    protected $userRoles;

    /**
     * Populated from session
     * @JMS\Type("AppBundle\Entity\Department")
     */
    public $currentDepartment;

    /**
     * Populated from session
     * @JMS\Type("string")
     */
    public $appMessage;

     /**
     * Populated from session
     * @JMS\Type("array")
     */
    public $roleHierarchy;

     /**
     * Populated when password changed
     * @JMS\Type("string")
     */
    public $newPassword;

     /**
     * @JMS\VirtualProperty
     */
     public function isAccountOwner()
     {
        return ($this->getOrganization()->getAccount()->getOwner() === $this);
     }

    /**
     * @inheritDoc
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        // Using bcrypt, so no salt needed.
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        $map = function(UserRole $userRole) {
            return $userRole->getRole()->getRole();
        };
        $roles = $this->userRoles->map($map)->toArray();
        return $roles;
    }

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function eraseCredentials()
    {
        //No plaintext password or sensitive info stored on object, so no-op
    }

    /**
     * @see \Serializable::serialize()
     * @codeCoverageIgnore
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            $this->isActive,
            // see section on salt below
            // $this->salt,
        ));
    }

    /**
     * @see \Serializable::unserialize()
     * @codeCoverageIgnore
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            $this->isActive,
            // see section on salt below
            // $this->salt
        ) = unserialize($serialized);
    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->isActive;
    }

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
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

     /**
     * Set organization
     *
     * @param \AppBundle\Entity\Organization $organization
     * @return User
     */
    public function getOrganization()
    {
        return $this->organization;
    }


    /**
     * Get organization
     *
     * @return \AppBundle\Entity\Organization
     */
    public function setOrganization(Organization $organization)
    {
        $this->organization = $organization;
        return $this;
    }

    /**
     * Set defaultDepartment
     *
     * @param \AppBundle\Entity\Department $defaultDepartment
     * @return User
     */
    public function setDefaultDepartment(Department $defaultDepartment = null)
    {
        $this->defaultDepartment = $defaultDepartment;

        return $this;
    }

    /**
     * Get defaultDepartment
     *
     * @return \AppBundle\Entity\Department
     */
    public function getDefaultDepartment()
    {
        return $this->defaultDepartment;
    }

    /**
     * Add roles
     *
     * @param \AppBundle\Entity\Role $roles
     * @return User
     */
    public function addUserRole(UserRole $userRole)
    {
        $this->userRoles[] = $userRole;

        return $this;
    }

    /**
     * Remove roles
     *
     * @param \AppBundle\Entity\Role $roles
     */
    public function removeUserRole(UserRole $userRole)
    {
        $this->userRoles->removeElement($userRole);
    }

    public function getUserRoles(){
        return $this->userRoles;
    }

    public function __construct()
    {
        $this->isActive = true;
        $this->userRoles = new ArrayCollection();
    }

    public function isOwnedByOrganization(Organization $organization)
    {
        return ($this->getOrganization() === $organization);
    }
}
