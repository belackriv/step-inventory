<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Role;

class RoleTest extends EntityTestCase
{
	public $entity = null;

	protected function setUp()
    {
        $this->entity = new Role();
    }

	public function testGettersAndSetters()
	{
		$this->assertInstanceOf('AppBundle\Entity\Role',$this->entity);
		return array(
			'id'=>array(null, false, true),
			'name'=>array('User', true, true),
			'role'=>array('ROLE_USER', true, true),
			'isAllowedToSwitch'=>array(false,  true, true)
		);
	}

	function testRoleHierarchy(){
		$parentRole = new \AppBundle\Entity\Role();

		$this->entity->addRoleToHierarchy($parentRole);
		$this->assertEquals(count( $this->entity->getRoleHierarchy() ), 1);
		$this->entity->removeRoleFromHierarchy($parentRole);
		$this->assertEquals(count( $this->entity->getRoleHierarchy() ), 0);
		//different names from doctrine generated entities, same functionality as above;
		$this->entity->addRoleHierarchy($parentRole);
		$this->assertEquals(count( $this->entity->getRoleHierarchy() ), 1);
		$this->entity->removeRoleHierarchy($parentRole);
		$this->assertEquals(count( $this->entity->getRoleHierarchy() ), 0);
	}
}