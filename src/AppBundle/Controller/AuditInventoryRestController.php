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


class AuditInventoryRestController extends FOSRestController
{

    use Mixin\RestPatchMixin;
    use Mixin\UpdateAclMixin;
    use Mixin\WampUpdatePusher;

    /**
     * @Rest\Get("/inventory_audit")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function listInventoryAuditAction(Request $request)
    {
        $page = (int)$request->query->get('page') - 1;
        $perPage =(int)$request->query->get('per_page');
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('COUNT(ia.id)')
            ->from('AppBundle:InventoryAudit', 'ia');

        $totalItems = $qb->getQuery()->getSingleScalarResult();

        Utilities::setupSearchableEntityQueryBuild($qb, $request);

        $totalCount = $qb->getQuery()->getSingleScalarResult();

        $qb->select('ia')
            ->orderBy('ia.id', 'DESC')
            ->setMaxResults($perPage)
            ->setFirstResult($page*$perPage);

        $items = $qb->getQuery()->getResult();

        $itemlist = array();
        $authorizationChecker = $this->get('security.authorization_checker');
        foreach($items as $item){
            if( true === $authorizationChecker->isGranted('VIEW', $item) and
                $item->isOwnedByOrganization($this->getUser()->getOrganization())
            ){
                $itemlist[] = $item;
            }
        }

        return ['total_count'=> (int)$totalCount, 'total_items' => (int)$totalItems, 'list'=>$itemlist];
    }

    /**
     * @Rest\Get("/inventory_audit/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getInventoryAuditAction(\AppBundle\Entity\InventoryAudit $inventoryAudit)
    {
        if( $this->get('security.authorization_checker')->isGranted('VIEW', $inventoryAudit) and
            $inventoryAudit->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            return $inventoryAudit;
        }else{
            throw $this->createNotFoundException('InventoryAudit #'.$inventoryAudit->getId().' Not Found');
        }
    }

    /**
     * @Rest\Post("/inventory_audit")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("inventoryAudit", converter="fos_rest.request_body")
     */
    public function createInventoryAuditAction(\AppBundle\Entity\InventoryAudit $inventoryAudit)
    {
        $inventoryAudit->setByUser($this->getUser());
        if( $this->get('security.authorization_checker')->isGranted('CREATE', $inventoryAudit) and
            $inventoryAudit->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            $em = $this->getDoctrine()->getManager();
            $inventoryAudit->setStartedAt(new \DateTime());
            $inventoryAudit->setIsCompleted(false);
            $inventoryAudit->getForBin()->setIsLocked(true);
            $em->persist($inventoryAudit);
            $em->flush();
            $this->updateAclByRoles($inventoryAudit, ['ROLE_USER'=>'view', 'ROLE_ADMIN'=>'operator']);
            return $inventoryAudit;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

     /**
     * @Rest\Put("/inventory_audit/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("inventoryAudit", converter="fos_rest.request_body")
     */
    public function updateInventoryAuditAction(\AppBundle\Entity\InventoryAudit $inventoryAudit)
    {
        if($this->get('security.authorization_checker')->isGranted('EDIT', $inventoryAudit)){
            $em = $this->getDoctrine()->getManager();
            $em->detach($inventoryAudit);
            $liveInventoryAudit = $this->getDoctrine()->getRepository('AppBundle:InventoryAudit')->findOneById($inventoryAudit->getId());
            if( $inventoryAudit->isOwnedByOrganization($this->getUser()->getOrganization()) and
                $liveInventoryAudit->isOwnedByOrganization($this->getUser()->getOrganization())
            ){
                $liveIsCompleted = $liveInventoryAudit->getIsCompleted();
                $inventoryAudit = $em->merge($inventoryAudit);
                $inventoryMovements = [];
                if($inventoryAudit->getEndedAt() and $liveIsCompleted === false){
                    $deviationBin = $this->getDoctrine()->getRepository('AppBundle:Bin')->findDeviationBin($inventoryAudit->getForBin());
                    $inventoryAudit->getForBin()->setIsLocked(false);
                    if(!$deviationBin){
                        throw new HttpException(Response::HTTP_CONFLICT, 'The Required Deviation Bin Was Not Found!' );
                    }
                    $inventoryMovements = $inventoryAudit->end($deviationBin);
                    foreach($inventoryMovements as $movement){
                        $em->persist($movement);
                    }
                }
                $em->flush();
                foreach($inventoryMovements as $movement){
                    $this->updateAclByRoles($movement, ['ROLE_USER'=>['view', 'edit'], 'ROLE_ADMIN'=>'operator']);
                }
                return $inventoryAudit;
            }else{
                throw $this->createAccessDeniedException();
            }
        }else{
            throw $this->createAccessDeniedException();
        }


    }

    /**
     * @Rest\Post("/inventory_sku_audit")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("inventorySkuAudit", converter="fos_rest.request_body")
     */
    public function createInventorySkuAuditAction(\AppBundle\Entity\InventorySkuAudit $inventorySkuAudit)
    {
        if( $this->get('security.authorization_checker')->isGranted('CREATE', $inventorySkuAudit) and
            $inventorySkuAudit->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            try{
                $inventorySkuAudit->isValid($this->getUser());
            }catch(\Exception $e){
                throw new HttpException(Response::HTTP_UNPROCESSABLE_ENTITY, $e->getMessage() );
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($inventorySkuAudit);
            $binSkuCount = $binSkuCount = $this->getDoctrine()->getRepository('AppBundle:BinSkuCount')
                ->findOneBy([
                    'bin' => $inventorySkuAudit->getInventoryAudit()->getForBin(),
                    'sku' => $inventorySkuAudit->getSku()
                ]);
            if(!$binSkuCount){
                $inventorySkuAudit->setSystemCount(0);
            }else{
                $inventorySkuAudit->setSystemCount($binSkuCount->getCount()) ;
            }

            $em->flush();
            $this->updateAclByRoles($inventorySkuAudit, ['ROLE_USER'=>'view', 'ROLE_ADMIN'=>'operator']);
            return $inventorySkuAudit;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

     /**
     * @Rest\Put("/inventory_sku_audit/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("inventorySkuAudit", converter="fos_rest.request_body")
     */
    public function updateInventorySkuAuditAction(\AppBundle\Entity\InventorySkuAudit $inventorySkuAudit)
    {
        if($this->get('security.authorization_checker')->isGranted('EDIT', $inventorySkuAudit)){
            $em = $this->getDoctrine()->getManager();
            $em->detach($inventorySkuAudit);
            $liveInventorySkuAudit = $this->getDoctrine()->getRepository('AppBundle:InventorySkuAudit')->findOneById($inventorySkuAudit->getId());
            if( $inventorySkuAudit->isOwnedByOrganization($this->getUser()->getOrganization()) and
                $liveInventorySkuAudit->isOwnedByOrganization($this->getUser()->getOrganization())
            ){
                try{
                    $inventorySkuAudit->isValid($this->getUser());
                }catch(\Exception $e){
                    throw new HttpException(Response::HTTP_UNPROCESSABLE_ENTITY, $e->getMessage() );
                }

                $em = $this->getDoctrine()->getManager();
                $inventorySkuAudit = $em->merge($inventorySkuAudit);
                $binSkuCount = $binSkuCount = $this->getDoctrine()->getRepository('AppBundle:BinSkuCount')
                    ->findOneBy([
                        'bin' => $inventorySkuAudit->getInventoryAudit()->getForBin(),
                        'sku' => $inventorySkuAudit->getSku()
                    ]);
                if(!$binSkuCount){
                    $inventorySkuAudit->setSystemCount(0);
                }else{
                    $inventorySkuAudit->setSystemCount($binSkuCount->getCount()) ;
                }

                $em->flush();
                return $inventorySkuAudit;
            }else{
                throw $this->createAccessDeniedException();
            }
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Delete("/inventory_sku_audit/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function deleteInventorySkuAuditAction(\AppBundle\Entity\InventorySkuAudit $inventorySkuAudit)
    {
        if( $this->get('security.authorization_checker')->isGranted('DELETE', $inventorySkuAudit) and
            $inventorySkuAudit->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            $em = $this->getDoctrine()->getManager();
            $em->remove($inventorySkuAudit);
            $em->flush();
            return $inventorySkuAudit;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Post("/inventory_tid_audit")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("inventoryTravelerIdAudit", converter="fos_rest.request_body")
     */
    public function createInventoryTravelerIdAuditAction(\AppBundle\Entity\InventoryTravelerIdAudit $inventoryTravelerIdAudit)
    {
        if( $this->get('security.authorization_checker')->isGranted('CREATE', $inventoryTravelerIdAudit) and
            $inventoryTravelerIdAudit->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            $travelerId = $this->getDoctrine()->getRepository('AppBundle:TravelerId')
                ->findOneBy(['label' => $inventoryTravelerIdAudit->getTravelerIdLabel()]);

            $inventoryTravelerIdAudit->setTravelerId($travelerId);

            try{
                $inventoryTravelerIdAudit->isValid($this->getUser());
            }catch(\Exception $e){
                throw new HttpException(Response::HTTP_UNPROCESSABLE_ENTITY, $e->getMessage() );
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($inventoryTravelerIdAudit);
            $em->flush();

            $this->updateAclByRoles($inventoryTravelerIdAudit, ['ROLE_USER'=>'view', 'ROLE_ADMIN'=>'operator']);
            return $inventoryTravelerIdAudit;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Delete("/inventory_tid_audit/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function deleteInventoryTravelerIdAuditAction(\AppBundle\Entity\InventoryTravelerIdAudit $inventoryTravelerIdAudit)
    {
        if( $this->get('security.authorization_checker')->isGranted('DELETE', $inventoryTravelerIdAudit) and
            $inventoryTravelerIdAudit->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            $em = $this->getDoctrine()->getManager();
            $em->remove($inventoryTravelerIdAudit);
            $em->flush();
            return $inventoryTravelerIdAudit;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Post("/inventory_sales_item_audit")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("inventorySalesItemAudit", converter="fos_rest.request_body")
     */
    public function createInventorySalesItemAuditAction(\AppBundle\Entity\InventorySalesItemAudit $inventorySalesItemAudit)
    {
        if( $this->get('security.authorization_checker')->isGranted('CREATE', $inventorySalesItemAudit) and
            $inventorySalesItemAudit->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            $salesItem = $this->getDoctrine()->getRepository('AppBundle:SalesItem')
                ->findOneBy(['label' => $inventorySalesItemAudit->getSalesItemLabel()]);

            $inventorySalesItemAudit->setSalesItem($salesItem);

            try{
                $inventorySalesItemAudit->isValid($this->getUser());
            }catch(\Exception $e){
                throw new HttpException(Response::HTTP_UNPROCESSABLE_ENTITY, $e->getMessage() );
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($inventorySalesItemAudit);
            $em->flush();

            $this->updateAclByRoles($inventorySalesItemAudit, ['ROLE_USER'=>'view', 'ROLE_ADMIN'=>'operator']);
            return $inventorySalesItemAudit;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Delete("/inventory_sales_item_audit/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function deleteInventorySalesItemAuditAction(\AppBundle\Entity\InventorySalesItemAudit $inventorySalesItemAudit)
    {
        if( $this->get('security.authorization_checker')->isGranted('DELETE', $inventorySalesItemAudit) and
            $inventorySalesItemAudit->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            $em = $this->getDoctrine()->getManager();
            $em->remove($inventorySalesItemAudit);
            $em->flush();
            return $inventorySalesItemAudit;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

}