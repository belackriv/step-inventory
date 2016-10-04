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
            if (true === $authorizationChecker->isGranted('VIEW', $item)){
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
        if( $this->get('security.authorization_checker')->isGranted('CREATE', $inventoryAudit) and
            $inventoryAudit->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            $em = $this->getDoctrine()->getManager();
            $inventoryAudit->setByUser($this->getUser());
            $inventoryAudit->setStartedAt(new \DateTime());
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
                $em->merge($inventoryAudit);
                $inventoryMovements = [];
                if($inventoryAudit->getEndedAt()){
                    $deviationBin = $this->getDoctrine()->getRepository('AppBundle:Bin')->findDeviationBin($inventoryAudit->getForBin());
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
     * @Rest\Post("/inventory_part_audit")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("inventoryPartAudit", converter="fos_rest.request_body")
     */
    public function createInventoryPartAuditAction(\AppBundle\Entity\InventoryPartAudit $inventoryPartAudit)
    {
        if( $this->get('security.authorization_checker')->isGranted('CREATE', $inventoryPartAudit) and
            $inventoryPartAudit->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            try{
                $inventoryPartAudit->isValid($this->getUser());
            }catch(\Exception $e){
                throw new HttpException(Response::HTTP_UNPROCESSABLE_ENTITY, $e->getMessage() );
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($inventoryPartAudit);
            $binPartCount = $binPartCount = $this->getDoctrine()->getRepository('AppBundle:BinPartCount')
                ->findOneBy([
                    'bin' => $inventoryPartAudit->getInventoryAudit()->getForBin(),
                    'part' => $inventoryPartAudit->getPart()
                ]);
            if(!$binPartCount){
                $inventoryPartAudit->setSystemCount(0);
            }else{
                $inventoryPartAudit->setSystemCount($binPartCount->getCount()) ;
            }

            $em->flush();
            $this->updateAclByRoles($inventoryPartAudit, ['ROLE_USER'=>'view', 'ROLE_ADMIN'=>'operator']);
            return $inventoryPartAudit;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

     /**
     * @Rest\Put("/inventory_part_audit/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("inventoryPartAudit", converter="fos_rest.request_body")
     */
    public function updateInventoryPartAuditAction(\AppBundle\Entity\InventoryPartAudit $inventoryPartAudit)
    {
        if($this->get('security.authorization_checker')->isGranted('EDIT', $inventoryPartAudit)){
            $em = $this->getDoctrine()->getManager();
            $em->detach($inventoryPartAudit);
            $liveInventoryPartAudit = $this->getDoctrine()->getRepository('AppBundle:InventoryPartAudit')->findOneById($inventoryPartAudit->getId());
            if( $inventoryPartAudit->isOwnedByOrganization($this->getUser()->getOrganization()) and
                $liveInventoryPartAudit->isOwnedByOrganization($this->getUser()->getOrganization())
            ){
                try{
                    $inventoryPartAudit->isValid($this->getUser());
                }catch(\Exception $e){
                    throw new HttpException(Response::HTTP_UNPROCESSABLE_ENTITY, $e->getMessage() );
                }

                $em = $this->getDoctrine()->getManager();
                $em->merge($inventoryPartAudit);
                $binPartCount = $binPartCount = $this->getDoctrine()->getRepository('AppBundle:BinPartCount')
                    ->findOneBy([
                        'bin' => $inventoryPartAudit->getInventoryAudit()->getForBin(),
                        'part' => $inventoryPartAudit->getPart()
                    ]);
                if(!$binPartCount){
                    $inventoryPartAudit->setSystemCount(0);
                }else{
                    $inventoryPartAudit->setSystemCount($binPartCount->getCount()) ;
                }

                $em->flush();
                return $inventoryPartAudit;
            }else{
                throw $this->createAccessDeniedException();
            }
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Delete("/inventory_part_audit/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function deleteInventoryPartAuditAction(\AppBundle\Entity\InventoryPartAudit $inventoryPartAudit)
    {
        if( $this->get('security.authorization_checker')->isGranted('DELETE', $inventoryPartAudit) and
            $inventoryPartAudit->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            $em = $this->getDoctrine()->getManager();
            $em->remove($inventoryPartAudit);
            $em->flush();
            return $inventoryPartAudit;
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
            $travelerId = $binTravelerIdCount = $this->getDoctrine()->getRepository('AppBundle:TravelerId')
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

}