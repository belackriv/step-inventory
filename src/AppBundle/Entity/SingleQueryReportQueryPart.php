<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation As JMS;
use \DateTime;


/**
 * @ORM\Entity
 * @ORM\Table()
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator", type="string")
 * @ORM\DiscriminatorMap({
 *  "main": "SingleQueryReportPart",
 *  "count": "SingleQueryReportCountPart",
 *  "parameter": "SingleQueryReportParameterPart"
 *  })
 * @JMS\Discriminator(field = "discriminator", map = {
 *      "main": "AppBundle\Entity\SingleQueryReportPart",
 *      "count": "AppBundle\Entity\SingleQueryReportCountPart",
 *      "parameter": "AppBundle\Entity\SingleQueryReportParameterPart"
 * })
 */
abstract class SingleQueryReportQueryPart
{

     /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Type("integer")
     */
    protected $id;

    public function getId(){
        return $this->id;
    }


   /**
     * @ORM\Column(type="string", length=64)
     * @JMS\Type("string")
     */
    protected $methodName;

    /**
     * Set methodName
     *
     * @param boolean $methodName
     * @return SingleQueryReportQueryPart
     */
    public function setMethodName($methodName){
        $this->methodName = $methodName;
        return $this;
    }

    /**
     * Get methodName
     *
     * @return string
     */
    public function getMethodName(){
        return $this->methodName;
    }


     /**
     * @ORM\Column(type="array")
     * @JMS\Type("array")
     */
    protected $args = [];

    /**
     * Set args
     *
     * @param array $args
     * @return SingleQueryReportQueryPart
     */
    public function setArgs(array $args){
        $this->args = $args;
        return $this;
    }

    /**
     * Get args
     *
     * @return array
     */
    public function getArgs(){
        return $this->args;
    }

    /**
     * @ORM\Column(type="integer", name="part_order")
     * @JMS\Type("integer")
     */
    protected $order;

    /**
     * Set order
     *
     * @param integer $order
     * @return SingleQueryReportQueryPart
     */
    public function setOrder($order)
    {
        $this->order = (integer)$order;
        return $this;
    }

    /**
     * Get order
     *
     * @return boolean
     */
    public function getOrder()
    {
        return $this->order;
    }
}