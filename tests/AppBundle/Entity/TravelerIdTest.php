<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\TravelerId;
use AppBundle\Entity\InboundOrder;
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
		$this->assertInstanceOf('AppBundle\Entity\TravelerId', $this->entity);
		return array(
			'id'=>array(null, false, true),
			'label'=>array('TestLabel', true, true),
		);
	}

	public function testGenerateLabel()
	{
		for ($i=1; $i < 100; $i++){
			$inboundOrder = new InboundOrder();
			$this->entity->setInboundOrder($inboundOrder);

			$inboundOrderReflection = new \ReflectionClass($inboundOrder);
			$inboundOrderTeflectionProperty = $inboundOrderReflection->getProperty('id');
			$inboundOrderTeflectionProperty->setAccessible(true);
			$inboundOrderTeflectionProperty->setValue($inboundOrder, rand(1, 10000000));
			$inboundOrder->generateLabel();
			for ($j=1; $j < rand(1,1000); $j++){
				$tid = new TravelerId();
				$inboundOrder->addTravelerId($tid);
				fwrite(STDERR, print_r("\n\t".$tid->generateLabel(), TRUE));
			}
			fwrite(STDERR, print_r("\n".$this->entity->generateLabel(), TRUE));
		}

	}

}