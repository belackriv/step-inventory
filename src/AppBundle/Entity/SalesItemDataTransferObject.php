<?php

namespace AppBundle\Entity;

use JMS\Serializer\Annotation As JMS;

Class SalesItemDataTransferObject
{

	/**
     * @JMS\Type("integer")
     */
	public $id = null;

	/**
	 * @JMS\Type("AppBundle\Entity\OutboundOrder")
	 */
	public $outboundOrder = null;

	/**
	 * @JMS\Type("AppBundle\Entity\Bin")
	 */
	public $bin = null;

	/**
     * @JMS\Type("boolean")
     */
	public $isVoid = null;

	/**
	 * @JMS\Type("string")
	 */
	public $revenue = null;

}
