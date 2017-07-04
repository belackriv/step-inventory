<?php

namespace AppBundle\Controller;

use AppBundle\Library\Service\MassImportAndExportService;
use AppBundle\Library\Service\CsvDirectDownloadService AS Csv;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\RestBundle\Controller\Annotations AS Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Collections\ArrayCollection;


class AdminImportRestController extends FOSRestController
{
    use Mixin\UpdateAclMixin;

    /**
     * @Rest\Post("/mass_import")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("massImport", converter="fos_rest.request_body")
     */
    public function importAction(\AppBundle\Entity\MassImport $massImport)
    {
        if($this->get('security.authorization_checker')->isGranted('CREATE', $massImport)){
            $importer = new MassImportAndExportService();
            $importer->setContainer($this->container);
            return $importer->import($massImport);
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Get("/export/{type}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function exportAction(Request $request, $type)
    {
        $exporter = new MassImportAndExportService();
        $exporter->setContainer($this->container);
        Csv::sendResponse($type.'_export', $exporter->export($type), $request);
    }

}