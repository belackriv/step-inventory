<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Department;
use Doctrine\Common\Collections\ArrayCollection;

class DepartmentTest extends EntityTestCase
{
	public $entity = null;

	protected function setUp()
    {
        $this->entity = new Department();
    }

	public function testGettersAndSetters()
	{
		$this->assertInstanceOf('AppBundle\Entity\Department',$this->entity);
		return array(
			'id'=>array(null, false, true),
			'name'=>array('TestDept', true, true),
			'office'=>array(new \AppBundle\Entity\Office(), true, true),

		);
	}

	public function testMenuItems(){
		$menuItem = new \AppBundle\Entity\MenuItem();
		$menuItems = new ArrayCollection(array($menuItem));
		$this->entity->addMenuItem($menuItem);
		$this->assertEquals(count( $this->entity->getMenuItems() ), 1);
		$this->entity->removeMenuItem($menuItem);
		$this->assertEquals(count( $this->entity->getMenuItems() ), 0);
		$this->entity->setMenuItems($menuItems);
		$this->assertEquals(count( $this->entity->getMenuItems() ), 1);
	}
}