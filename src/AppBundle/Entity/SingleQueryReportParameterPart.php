<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation As JMS;

/**
 * @ORM\Entity
 */
class SingleQueryReportParameterPart extends SingleQueryReportQueryPart
{

    /**
     * @ORM\ManyToOne(targetEntity="SingleQueryReportParameter", inversedBy="parts")
     * @JMS\Type("AppBundle\Entity\SingleQueryReportParameter")
     */
    protected $singleQueryReportParameter;

    /**
     * Set singleQueryReportParameter
     *
     * @param \AppBundle\Entity\SingleQueryReportParameter  $singleQueryReportParameter
     * @return SingleQueryReportParameterPart
     */
    public function setSingleQueryReportParameter(SingleQueryReportParameter  $singleQueryReportParameter)
    {
        $this->singleQueryReportParameter = $singleQueryReportParameter;

        return $this;
    }

    /**
     * Get singleQueryReportParameter
     *
     * @return \AppBundle\Entity\SingleQueryReportParameter
     */
    public function getSingleQueryReportParameter()
    {
        return $this->singleQueryReportParameter;
    }

}
