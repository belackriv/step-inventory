<?php
namespace AppBundle\Serializer\Handler;

use JMS\Serializer\VisitorInterface;
use Doctrine\Common\Collections\Collection;
use JMS\Serializer\Context;

class ArrayCollectionHandler extends \JMS\Serializer\Handler\ArrayCollectionHandler
{
    public function serializeCollection(VisitorInterface $visitor, Collection $collection, array $type, Context $context)
    {
        // We change the base type, and pass through possible parameters.
        $type['name'] = 'array';
        //don't include items that will produce null elements
        $dataArray = [];
        foreach($collection->toArray() as $element){
        	if(!$context->isVisiting($element)){
        		$dataArray[] = $element;
        	}
        }
        return $visitor->visitArray($dataArray, $type, $context);
    }
}