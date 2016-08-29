<?php

namespace Tests\AppBundle\Entity;


abstract class EntityTestCase extends \PHPUnit_Framework_TestCase
{

	abstract public function testGettersAndSetters();

	/**
	* @depends testGettersAndSetters
	*/
	public function testEntityGettersAndSetters($propertyList)
	{
		foreach($propertyList as $propName => $elem){
			list($value, $testSetter, $testGetter) = $elem;
			if($testSetter){
				$setterMethodName = 'set'.ucfirst($propName);
				$this->entity->$setterMethodName($value);
			}
			if($testGetter){
				$getterMethodName = 'get'.ucfirst($propName);
				$this->assertEquals($value, $this->entity->$getterMethodName($value) );
			}
		}
	}
}