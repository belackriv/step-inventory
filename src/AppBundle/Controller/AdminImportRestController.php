<?php

namespace AppBundle\Controller;

use AppBundle\Library\Service\MassImporterService;

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
    public function createSkuAction(\AppBundle\Entity\MassImport $massImport)
    {
        if($this->get('security.authorization_checker')->isGranted('CREATE', $massImport)){
            $importer = new MassImporterService();
            $importer->setContainer($this->container);
            return $importer->import($massImport);
        }else{
            throw $this->createAccessDeniedException();
        }
    }



}