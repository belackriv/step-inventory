<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\MenuItem;
use Doctrine\Common\Collections\ArrayCollection;

class MenuItemTest extends EntityTestCase
{
	public $entity = null;

	protected function setUp()
    {
        $this->entity = new MenuItem();
    }

	public function testGettersAndSetters()
	{
		$this->assertInstanceOf('AppBundle\Entity\MenuItem',$this->entity);
		return array(
			'id'=>array(null, false, true),
			'isActive'=>array(true, true, true),
			'position'=>array(1, true, true),
			'menuLink'=>array(new \AppBundle\Entity\MenuLink(), true, true),
			'department'=>array(new \AppBundle\Entity\Department(), true, true),
			'parent'=>array(new MenuItem(), true, true),
		);
	}

	public function testIsActive()
	{
		$this->entity->isActive(false);
		$this->assertEquals($this->entity->isActive(), false);
	}

	public function testChildren(){
		$menuItem = new MenuItem();
		$menuItems = new ArrayCollection(array($menuItem));
		$this->entity->addChild($menuItem);
		$this->assertEquals(count( $this->entity->getChildren() ), 1);
		$this->entity->removeChild($menuItem);
		$this->assertEquals(count( $this->entity->getChildren() ), 0);
		$this->entity->setChildren($menuItems);
		$this->assertEquals(count( $this->entity->getChildren() ), 1);
	}
}