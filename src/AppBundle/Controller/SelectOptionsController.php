<?php

namespace AppBundle\Controller;

use AppBundle\Library\Utilities;
use AppBundle\Library\Service\UploadException;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FOS\RestBundle\Controller\Annotations AS Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Doctrine\Common\Collections\ArrayCollection;


class SelectOptionsController extends FOSRestController
{
    use Mixin\RestPatchMixin;
    use Mixin\UpdateAclMixin;
    use Mixin\WampUpdatePusher;


    /**
     * @Rest\Get("/select_options/inbound_order")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function listSelectOptionsForInboundOrderAction(Request $request)
    {
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('io')
            ->from('AppBundle:InboundOrder', 'io')
            ->join('io.client', 'c')
            ->where('c.organization = :org')
            ->setParameter('org', $this->getUser()->getOrganization());

        Utilities::setupSearchableEntityQueryBuild($qb, $request);

        $qb->select('io')->orderBy('io.label', 'ASC');

        $items = $qb->getQuery()->getResult();

        $itemlist = array();
        $authorizationChecker = $this->get('security.authorization_checker');
        foreach($items as $item){
            if (true === $authorizationChecker->isGranted('VIEW', $item)){
                $itemlist[] = $item->getSelectOptionData();
            }
        }

        return ['total_count'=> count($itemlist), 'total_items' => count($itemlist), 'list'=>$itemlist];
    }


    /**
     * @Rest\Get("/select_options/sku")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function listSelectOptionsForSkuAction(Request $request)
    {
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('s')
            ->from('AppBundle:Sku', 's')
            ->where('s.organization = :org')
            ->setParameter('org', $this->getUser()->getOrganization());

        Utilities::setupSearchableEntityQueryBuild($qb, $request);

        $qb->select('s')->orderBy('s.name', 'ASC');

        $items = $qb->getQuery()->getResult();

        $itemlist = array();
        $authorizationChecker = $this->get('security.authorization_checker');
        foreach($items as $item){
            if (true === $authorizationChecker->isGranted('VIEW', $item)){
                $itemlist[] = $item->getSelectOptionData();
            }
        }

        return ['total_count'=> count($itemlist), 'total_items' => count($itemlist), 'list'=>$itemlist];
    }

    /**
     * @Rest\Get("/select_options/bin")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function listSelectOptionsForBinAction(Request $request)
    {
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('b')
            ->from('AppBundle:Bin', 'b')
            ->join('b.department', 'd')
            ->join('d.office', 'o')
            ->where('o.organization = :org')
            ->setParameter('org', $this->getUser()->getOrganization());

        Utilities::setupSearchableEntityQueryBuild($qb, $request);

        $qb->select('b')->orderBy('b.name', 'ASC');

        $items = $qb->getQuery()->getResult();

        $itemlist = array();
        $authorizationChecker = $this->get('security.authorization_checker');
        foreach($items as $item){
            if (true === $authorizationChecker->isGranted('VIEW', $item)){
                $itemlist[] = $item->getSelectOptionData();
            }
        }

        return ['total_count'=> count($itemlist), 'total_items' => count($itemlist), 'list'=>$itemlist];
    }

    /**
     * @Rest\Get("/select_options/part")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function listSelectOptionsForPartAction(Request $request)
    {
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('p')
            ->from('AppBundle:Part', 'p')
            ->where('p.organization = :org')
            ->setParameter('org', $this->getUser()->getOrganization());

        Utilities::setupSearchableEntityQueryBuild($qb, $request);

        $qb->select('p')->orderBy('p.name', 'ASC');

        $items = $qb->getQuery()->getResult();

        $itemlist = array();
        $authorizationChecker = $this->get('security.authorization_checker');
        foreach($items as $item){
            if (true === $authorizationChecker->isGranted('VIEW', $item)){
                $itemlist[] = $item->getSelectOptionData();
            }
        }

        return ['total_count'=> count($itemlist), 'total_items' => count($itemlist), 'list'=>$itemlist];
    }


    /**
     * @Rest\Get("/select_options/commodity")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function listSelectOptionsForCommodityAction(Request $request)
    {
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('c')
            ->from('AppBundle:Commodity', 'c')
            ->where('c.organization = :org')
            ->setParameter('org', $this->getUser()->getOrganization());

        Utilities::setupSearchableEntityQueryBuild($qb, $request);

        $qb->select('c')->orderBy('c.name', 'ASC');

        $items = $qb->getQuery()->getResult();

        $itemlist = array();
        $authorizationChecker = $this->get('security.authorization_checker');
        foreach($items as $item){
            if (true === $authorizationChecker->isGranted('VIEW', $item)){
                $itemlist[] = $item->getSelectOptionData();
            }
        }

        return ['total_count'=> count($itemlist), 'total_items' => count($itemlist), 'list'=>$itemlist];
    }

    /**
     * @Rest\Get("/select_options/unit_type")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function listSelectOptionsForUnitTypeAction(Request $request)
    {
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('ut')
            ->from('AppBundle:UnitType', 'ut')
            ->where('ut.organization = :org')
            ->setParameter('org', $this->getUser()->getOrganization());

        Utilities::setupSearchableEntityQueryBuild($qb, $request);

        $qb->select('ut')->orderBy('ut.name', 'ASC');

        $items = $qb->getQuery()->getResult();

        $itemlist = array();
        $authorizationChecker = $this->get('security.authorization_checker');
        foreach($items as $item){
            if (true === $authorizationChecker->isGranted('VIEW', $item)){
                $itemlist[] = $item->getSelectOptionData();
            }
        }

        return ['total_count'=> count($itemlist), 'total_items' => count($itemlist), 'list'=>$itemlist];
    }


}
