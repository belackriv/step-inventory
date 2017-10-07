<?php

namespace AppBundle\Controller;

use AppBundle\Library\Utilities;
use AppBundle\Library\Service\UploadException;
use AppBundle\Library\Service\SncRedisSessionQueryService;
use AppBundle\Library\Service\MonthlyTravelerIdLimitService;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FOS\RestBundle\Controller\Annotations AS Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Doctrine\Common\Collections\ArrayCollection;


class WebhookRestController extends FOSRestController
{

    /**
     * @Rest\Post("/webhook/stripe_invoice_created")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("stripeEvent",  class="stdClass", converter="fos_rest.request_body")
     */
    public function createInvoiceOverageAction($stripeEvent, Request $request)
    {
        \Stripe\Stripe::setApiKey($this->container->getParameter('stripe_secure_key'));
        $monthlyTravelerIdLimitService = new MonthlyTravelerIdLimitService($this->container);
        $org = $monthlyTravelerIdLimitService->getOrganizationFromInvoicData($stripeEvent['data']['object']);
        if(!$org){
            throw new HttpException(500, 'No Organization Found from Invoice customer');
        }
        $monthlyTravelerIdLimitService->processMonthlyTravelerIdOverageForStripe($org, $stripeEvent['data']['object']);
        return ['webhook_processed' => true];
    }

}