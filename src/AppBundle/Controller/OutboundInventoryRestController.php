<?php

namespace AppBundle\Controller;

use AppBundle\Library\Utilities;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FOS\RestBundle\Controller\Annotations AS Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Doctrine\Common\Collections\ArrayCollection;


class OutboundInventoryRestController extends FOSRestController
{

    use Mixin\RestPatchMixin;
    use Mixin\UpdateAclMixin;
    use Mixin\WampUpdatePusher;
    use Mixin\SalesItemLogMixin;

    /**
     * @Rest\Get("/sales_item")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function listSalesItemAction(Request $request)
    {
        $page = (int)$request->query->get('page') - 1;
        $perPage =(int)$request->query->get('per_page');
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('COUNT(si.id)')
            ->from('AppBundle:SalesItem', 'si')
            ->join('si.sku', 's')
            ->leftJoin('si.outboundOrder', 'oo')
            ->where('s.organization = :org')
            ->setParameter('org', $this->getUser()->getOrganization());

        $totalItems = $qb->getQuery()->getSingleScalarResult();

        Utilities::setupSearchableEntityQueryBuild($qb, $request);

        $totalCount = $qb->getQuery()->getSingleScalarResult();

        $qb->select('si')
            ->orderBy('si.id', 'DESC')
            ->setMaxResults($perPage)
            ->setFirstResult($page*$perPage);

        $items = $qb->getQuery()->getResult();

        $itemlist = array();
        $authorizationChecker = $this->get('security.authorization_checker');
        foreach($items as $item){
            if (true === $authorizationChecker->isGranted('VIEW', $item)){
                $itemlist[] = $item;
            }
        }

        return ['total_count'=> (int)$totalCount, 'total_items' => (int)$totalItems, 'list'=>$itemlist];
    }

    /**
     * @Rest\Get("/sales_item/{id}")
     * @Rest\Get("/show/sales_item/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getSalesItemAction(\AppBundle\Entity\SalesItem $salesItem)
    {
        if( $this->get('security.authorization_checker')->isGranted('VIEW', $salesItem) and
            $salesItem->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            return $salesItem;
        }else{
            throw $this->createNotFoundException('SalesItem #'.$salesItem->getId().' Not Found');
        }
    }

    /**
     * @Rest\Put("/sales_item/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("salesItemDto", converter="fos_rest.request_body")
     */
    public function updateSalesItemAction(\AppBundle\Entity\SalesItemDataTransferObject $salesItemDto)
    {
        $em = $this->getDoctrine()->getManager();
        $salesItem = $em->getRepository('AppBundle:SalesItem')->findOneById($salesItemDto->id);
        if( $this->get('security.authorization_checker')->isGranted('EDIT', $salesItem) and
            $salesItem->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            $edit = $this->checkForSalesItemEdit($salesItem, $salesItemDto);
            $move = $this->checkForSalesItemMovement($salesItem, $salesItemDto);
            $salesItem->assignPropertiesFromDataTransferObject($salesItemDto);
            $em->flush();
            if($edit){
                $this->updateAclByRoles($edit, ['ROLE_USER'=>['view'], 'ROLE_ADMIN'=>'operator']);
            }
            if($move){
                $this->updateAclByRoles($move, ['ROLE_USER'=>['view'], 'ROLE_ADMIN'=>'operator']);
            }
            return $salesItem;

        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Delete("/sales_item/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function deleteSalesItemAction(\AppBundle\Entity\SalesItem $salesItem)
    {
        if( $this->get('security.authorization_checker')->isGranted('DELETE', $salesItem) and
            $salesItem->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            $em = $this->getDoctrine()->getManager();
            $em->remove($salesItem);
            $em->flush();
            return $salesItem;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Put("/mass_sales_item/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("massSalesItem", converter="fos_rest.request_body")
     */
    public function updateMassSalesItemAction(\AppBundle\Entity\MassSalesItem $massSalesItem)
    {
        set_time_limit(300);
        ini_set('memory_limit','1024M');
        $em = $this->getDoctrine()->getManager();
        $travelerIdLogEntities = [];
        $transformEntities = [];
        foreach($massSalesItem->getSalesItems() as $travelerId){
            if($this->get('security.authorization_checker')->isGranted('EDIT', $travelerId)){
                $em->detach($travelerId);
                $liveSalesItem = $em->getRepository('AppBundle:SalesItem')->findOneById($travelerId->getId());
                if( !$travelerId->isOwnedByOrganization($this->getUser()->getOrganization()) or
                    !$liveSalesItem->isOwnedByOrganization($this->getUser()->getOrganization())
                ){
                    throw $this->createAccessDeniedException();
                }
            }else{
                throw $this->createAccessDeniedException();
            }
        }

        if($massSalesItem->isTransform()){
            foreach($massSalesItem->getSalesItems() as $travelerId){
                $em->merge($travelerId);
            }
            foreach($massSalesItem->getSalesItems() as $travelerId){
                $transformEntities[] = $this->createTransformEntities($travelerId);
                $travelerIdLogEntities[] = $travelerId->getTransform();
            }
        }else{
            foreach($massSalesItem->getSalesItems() as $travelerId){
                $edit = $this->checkForSalesItemEdit($liveSalesItem, $travelerId);
                $move = $this->checkForSalesItemMovement($liveSalesItem, $travelerId);
                if($edit){
                    $travelerIdLogEntities[] = $edit;
                }
                if($move){
                    $travelerIdLogEntities[] = $move;
                }
                $em->merge($travelerId);
            }
        }

        $em->flush();

        foreach($travelerIdLogEntities as $logEntity){
            $this->updateAclByRoles($logEntity, ['ROLE_USER'=>['view'], 'ROLE_ADMIN'=>'operator']);
        }

        foreach($transformEntities as $logEntity){
            $this->updateAclByRoles($logEntity, ['ROLE_USER'=>['view', 'edit'], 'ROLE_ADMIN'=>'operator']);
        }
        return $massSalesItem;
    }

    /**
     * @Rest\Get("/inventory_sales_item_edit")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function listInventorySalesItemEditAction(Request $request)
    {
        $page = (int)$request->query->get('page') - 1;
        $perPage =(int)$request->query->get('per_page');
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('COUNT(ite.id)')
            ->from('AppBundle:InventorySalesItemEdit', 'ite')
            ->join('ite.travelerId', 'tid')
            ->join('tid.inboundOrder', 'o')
            ->join('o.client', 'c')
            ->where('c.organization = :org')
            ->setParameter('org', $this->getUser()->getOrganization());

        $totalItems = $qb->getQuery()->getSingleScalarResult();

        Utilities::setupSearchableEntityQueryBuild($qb, $request);

        $totalCount = $qb->getQuery()->getSingleScalarResult();

        $qb->select('ite')
            ->orderBy('ite.id', 'DESC')
            ->setMaxResults($perPage)
            ->setFirstResult($page*$perPage);

        $items = $qb->getQuery()->getResult();

        $itemlist = array();
        $authorizationChecker = $this->get('security.authorization_checker');
        foreach($items as $item){
            if (true === $authorizationChecker->isGranted('VIEW', $item)){
                $itemlist[] = $item;
            }
        }

        return ['total_count'=> (int)$totalCount, 'total_items' => (int)$totalItems, 'list'=>$itemlist];
    }

    /**
     * @Rest\Get("/inventory_sales_item_edit/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getInventorySalesItemEditAction(\AppBundle\Entity\InventorySalesItemEdit $inventorySalesItemEdit)
    {
        if( $this->get('security.authorization_checker')->isGranted('VIEW', $inventorySalesItemEdit) and
            $inventorySalesItemEdit->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            return $inventorySalesItemEdit;
        }else{
            throw $this->createNotFoundException('InventorySalesItemEdit #'.$inventorySalesItemEdit->getId().' Not Found');
        }
    }


    /**
     * @Rest\Get("/inventory_sales_item_movement")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function listInventorySalesItemMovementAction(Request $request)
    {
        $page = (int)$request->query->get('page') - 1;
        $perPage =(int)$request->query->get('per_page');
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('COUNT(itm.id)')
            ->from('AppBundle:InventorySalesItemMovement', 'itm')
            ->join('itm.travelerId', 'sales_item')
            ->join('sales_item.inboundOrder', 'o')
            ->join('o.client', 'c')
            ->where('c.organization = :org')
            ->setParameter('org', $this->getUser()->getOrganization());

        $totalItems = $qb->getQuery()->getSingleScalarResult();

        Utilities::setupSearchableEntityQueryBuild($qb, $request);

        $totalCount = $qb->getQuery()->getSingleScalarResult();

        $qb->select('itm')
            ->orderBy('itm.id', 'DESC')
            ->setMaxResults($perPage)
            ->setFirstResult($page*$perPage);

        $items = $qb->getQuery()->getResult();

        $itemlist = array();
        $authorizationChecker = $this->get('security.authorization_checker');
        foreach($items as $item){
            if (true === $authorizationChecker->isGranted('VIEW', $item)){
                $itemlist[] = $item;
            }
        }

        return ['total_count'=> (int)$totalCount, 'total_items' => (int)$totalItems, 'list'=>$itemlist];
    }

    /**
     * @Rest\Get("/inventory_sales_item_movement/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getInventorySalesItemMovementAction(\AppBundle\Entity\InventorySalesItemMovement $inventorySalesItemMovement)
    {
        if( $this->get('security.authorization_checker')->isGranted('VIEW', $inventorySalesItemMovement) and
            $inventorySalesItemMovement->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            return $inventorySalesItemMovement;
        }else{
            throw $this->createNotFoundException('InventorySalesItemMovement #'.$inventorySalesItemMovement->getId().' Not Found');
        }
    }


}