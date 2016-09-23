<?php

namespace AppBundle\Controller;

use AppBundle\Library\Utilities;
use AppBundle\Library\Service\ExcelDirectDownloadService AS Excel;
use AppBundle\Library\Service\CsvDirectDownloadService AS Csv;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FOS\RestBundle\Controller\Annotations AS Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Doctrine\Common\Collections\ArrayCollection;


class ReportingRestController extends FOSRestController
{

    use Mixin\RestPatchMixin;
    use Mixin\UpdateAclMixin;

    /**
     * @Rest\Get("/reporting")
     * @Rest\Get("/reporting/single_query_report")
     * @Rest\Get("/reporting/single_query_report/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function reportingAction()
    {
        return [];
    }


    /**
     * @Rest\Get("/single_query_report")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function listSingleQueryReportsAction()
    {
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('sqr')
            ->from('AppBundle:SingleQueryReport', 'sqr')
            ->where('sqr.isEnabled = :true')
            ->orderBy('sqr.name', 'ASC')
            ->setParameter('true', true);

        $items = $qb->getQuery()->getResult();

        $itemlist = array();
        $authorizationChecker = $this->get('security.authorization_checker');
        foreach($items as $item){
            if (true === $authorizationChecker->isGranted('VIEW', $item)){
                $itemlist[] = $item;
                $item->setChoices($this->container);
            }
        }

        return ['total_count'=> (int)$itemlist, 'total_items' => (int)$itemlist, 'list'=>$itemlist];
    }

    /**
     * @Rest\Get("/single_query_report/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getSingleQueryReportAction(\AppBundle\Entity\SingleQueryReport $singleQueryReport)
    {
        $accounts = $this->getAccessibleAccounts();
        if($this->getAccessibleAccountIds() !== null and $singleQueryReport->getAvailableToAccounts() !== true){
            throw new HttpException(403,'This Query is not available to Account Users');
        }
        $singleQueryReport->setChoices($this->container);
        return $singleQueryReport;
    }

    /**
     * @Rest\Get("/single_query_report/{id}/run")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function runSingleQueryReportAction(\AppBundle\Entity\SingleQueryReport $singleQueryReport, Request $request)
    {
        try{
            $data = $singleQueryReport->run($this->container, $request);
        } catch(\Exception $e){
            $httpCode = ($e->getCode() > 0)?$e->getCode():500;
            throw new HttpException($httpCode, $e->getMessage());
        }
        return $data;
    }

    /**
     * @Rest\Get("/single_query_report/{id}/export")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function exportSingleQueryReportAction(\AppBundle\Entity\SingleQueryReport $singleQueryReport, Request $request)
    {
        try{
            $results = $singleQueryReport->export($this->container, $request);
        } catch(\Exception $e){
            $httpCode = ($e->getCode() > 0)?$e->getCode():500;
            throw new HttpException($httpCode, $e->getMessage());
        }
        if(count($results['data']) * count($results['columns']) > 25000){
            return $this->render('default:error.html.twig', [
                'errorMessage' => 'The generated file would take to long to create, please go back and use the csv option.'
            ]);
        }
        $headerRow = [];
        foreach($results['columns'] as $column){
            $headerRow[] = [$column['label'], Excel::FORMAT_STRING];
        }
        $data = [$headerRow];
        foreach($results['data'] as $row){
            $dataRow = [];
            foreach($results['columns'] as $column){
                $dataRow[] = [$row[$column['name']], Excel::getFormatFromName($column['type'])];
            }
            $data[] = $dataRow;
        }

        Excel::sendResponse($singleQueryReport->getFileName(), $data, $request);
    }

     /**
     * @Rest\Get("/single_query_report/{id}/export_csv")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function exportCsvSingleQueryReportAction(\AppBundle\Entity\SingleQueryReport $singleQueryReport, Request $request)
    {
         try{
            $results = $singleQueryReport->export($this->container, $request);
        } catch(\Exception $e){
            $httpCode = ($e->getCode() > 0)?$e->getCode():500;
            throw new HttpException($httpCode, $e->getMessage());
        }
        $headerRow = [];
        foreach($results['columns'] as $column){
            $headerRow[] = $column['label'];
        }
        $data = [$headerRow];
        foreach($results['data'] as $row){
            $dataRow = [];
            foreach($results['columns'] as $column){
                $dataRow[] = Csv::getCastedValueFromFormat($row[$column['name']], Csv::getFormatFromName($column['type']));
            }
            $data[] = $dataRow;
        }

        Csv::sendResponse($singleQueryReport->getFileName(), $data, $request);
    }


}