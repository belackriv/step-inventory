<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class DefaultController extends Controller
{
    /**
     * @Security("has_role('ROLE_USER')")
     * @Route("/", name="homepage")
     * @Template(":default:index.html.twig")
     */
    public function getHomepageAction()
    {
        return array();
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route("/admin/{all}", name="admin", defaults={"all" = ""})
     * @Template(":default:index.html.twig")
     */
    public function getAdminAction()
    {
        return array();
    }
}

//curl -v -H "Accept: application/json" -H "Content-type: application/json" -X POST -d '{"name":"DFW"}' http://localhost/~belac/step-inventory/app_dev.php/office
//curl -v -H "Accept: application/json" -H "Content-type: application/json" http://localhost/~belac/step-inventory/app_dev.php/office