<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Office;
use Doctrine\Common\Collections\ArrayCollection;

class OfficeTest extends EntityTestCase
{
	public $entity = null;

	protected function setUp()
    {
        $this->entity = new Office();
    }

	public function testGettersAndSetters()
	{
		$this->assertInstanceOf('AppBundle\Entity\Office',$this->entity);
		return array(
			'id'=>array(null, false, true),
			'name'=>array('TestOffice', true, true),
		);
	}

	public function testDepartments(){
		$department = new \AppBundle\Entity\Department();
		$departments = new ArrayCollection(array($department));
		
		$this->entity->addDepartment($department);
		$this->assertEquals(count( $this->entity->getDepartments() ), 1);
		$this->entity->removeDepartment($department);
		$this->assertEquals(count( $this->entity->getDepartments() ), 0);
	}
}