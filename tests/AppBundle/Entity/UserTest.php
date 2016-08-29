<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\User;

class UserTest extends EntityTestCase
{
	public $entity = null;

	protected function setUp()
    {
        $this->entity = new User();
    }

	public function testGettersAndSetters()
	{
		$this->assertInstanceOf('AppBundle\Entity\User',$this->entity);
		return array(
			'id'=>array( null, false, true),
			'username'=>array( 'testuser', true, true),
			'password'=>array( 'password', true, true),
			'email'=>array( 'testuser@example.com', true, true),
			'firstName'=>array( 'test', true, true),
			'lastName'=>array( 'user', true, true),
			'isActive'=>array( true, true, true),
			'defaultDepartment'=>array( new \AppBundle\Entity\Department(), true, true)
		);
	}

	function testIsEnabledIfIsActive()
	{
		$this->entity->setIsActive(true);
		$this->assertEquals(true, $this->entity->isEnabled());
		$this->entity->setIsActive(false);
		$this->assertEquals(false, $this->entity->isEnabled());
	}

	function testUnusedAdvancedUserInterfaceMethods(){
		$this->assertTrue($this->entity->isAccountNonExpired());
		$this->assertTrue($this->entity->isAccountNonLocked());
		$this->assertTrue($this->entity->isCredentialsNonExpired());
		$this->assertNull($this->entity->getSalt());
	}

	function testUserRoles(){
		$userRole = new \AppBundle\Entity\Role();
		$this->entity->addRole($userRole);
		$this->assertEquals(count( $this->entity->getRoles() ), 1);
		$this->entity->removeRole($userRole);
		$this->assertEquals(count( $this->entity->getRoles() ), 0);
	}
}