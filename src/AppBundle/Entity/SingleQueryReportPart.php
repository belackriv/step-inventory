<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation As JMS;

/**
 * @ORM\Entity
 */
class SingleQueryReportPart extends SingleQueryReportQueryPart
{

    /**
     * @ORM\ManyToOne(targetEntity="SingleQueryReport", inversedBy="parts")
     * @JMS\Type("AppBundle\Entity\SingleQueryReport")
     */
    protected $singleQueryReport;

    /**
     * Set singleQueryReport
     *
     * @param \AppBundle\Entity\User $singleQueryReport
     * @return SingleQueryReportCountPart
     */
    public function setSingleQueryReport(SingleQueryReport $singleQueryReport)
    {
        $this->singleQueryReport = $singleQueryReport;

        return $this;
    }

    /**
     * Get singleQueryReport
     *
     * @return \AppBundle\Entity\SingleQueryReport
     */
    public function getSingleQueryReport()
    {
        return $this->singleQueryReport;
    }

}
