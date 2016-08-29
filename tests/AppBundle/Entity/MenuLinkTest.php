<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\MenuLink;
use Doctrine\Common\Collections\ArrayCollection;

class MenuLinkTest extends EntityTestCase
{
	public $entity = null;

	protected function setUp()
    {
        $this->entity = new MenuLink();
    }

	public function testGettersAndSetters()
	{
		$this->assertInstanceOf('AppBundle\Entity\MenuLink',$this->entity);
		return array(
			'id'=>array(null, false, true),
			'name'=>array('TestName', true, true),
			'url'=>array('TestURL', true, true),
			'appTrigger'=>array('test:trigger', true, true),
		);
	}
}