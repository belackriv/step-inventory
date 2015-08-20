<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\TravelerId;
use Doctrine\Common\Collections\ArrayCollection;

class TravelerIdTest extends EntityTestCase
{
	public $entity = null;

	protected function setUp()
    {
        $this->entity = new TravelerId();
    }

	public function testGettersAndSetters()
	{
		$this->assertInstanceOf('AppBundle\Entity\TravelerId',$this->entity);
		return array(
			'id'=>array(null, false, true),
			'travelerId'=>array('TestTID', true, true),
		);
	}

}