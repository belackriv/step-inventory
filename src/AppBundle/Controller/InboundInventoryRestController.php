<?php

namespace AppBundle\Controller;

use AppBundle\Library\Utilities;
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


class InboundInventoryRestController extends FOSRestController
{

    use Mixin\RestPatchMixin;
    use Mixin\UpdateAclMixin;
    use Mixin\WampUpdatePusher;
    use Mixin\TravelerIdLogMixin;
    use Mixin\TravelerIdTransformMixin;

    /**
     * @Rest\Get("/tid")
     * @Rest\Get("/inventory_action")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function listTravelerIdAction(Request $request)
    {
        $page = (int)$request->query->get('page') - 1;
        $perPage =(int)$request->query->get('per_page');
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('COUNT(tid.id)')
            ->from('AppBundle:TravelerId', 'tid')
            ->join('tid.sku', 'sku')
            ->where('sku.organization = :org')
            //->andWhere('tid.transform IS NULL')
            ->setParameter('org', $this->getUser()->getOrganization());

        $totalItems = $qb->getQuery()->getSingleScalarResult();

        Utilities::setupSearchableEntityQueryBuild($qb, $request);

        $totalCount = $qb->getQuery()->getSingleScalarResult();

        $qb->select('tid')
            ->orderBy('tid.id', 'DESC')
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
     * @Rest\Get("/tid/{id}")
     * @Rest\Get("/show/tid/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getTravelerIdAction(\AppBundle\Entity\TravelerId $travelerId)
    {
        if( $this->get('security.authorization_checker')->isGranted('VIEW', $travelerId) and
            $travelerId->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            return $travelerId;
        }else{
            throw $this->createNotFoundException('TravelerId #'.$travelerId->getId().' Not Found');
        }
    }

    /**
     * @Rest\Post("/tid")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("travelerId", converter="fos_rest.request_body")
     */
    public function createTravelerIdAction(\AppBundle\Entity\TravelerId $travelerId)
    {
        if( $this->get('security.authorization_checker')->isGranted('CREATE', $travelerId) and
            $travelerId->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            $em = $this->getDoctrine()->getManager();
            $em->persist($travelerId);
            $travelerId->generateLabel();
            $em->flush();
            $this->updateAclByRoles($travelerId, ['ROLE_USER'=>['view', 'edit'], 'ROLE_ADMIN'=>'operator']);
            return $travelerId;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Put("/tid/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("travelerId", converter="fos_rest.request_body")
     */
    public function updateTravelerIdAction(\AppBundle\Entity\TravelerId $travelerId)
    {

        if($this->get('security.authorization_checker')->isGranted('EDIT', $travelerId)){
            $em = $this->getDoctrine()->getManager();
            $em->detach($travelerId);
            if($travelerId->getTransform()){
                $em->detach($travelerId->getTransform());
            }
            if($travelerId->getReverseTransform()){
                $em->detach($travelerId->getReverseTransform());
            }

            $liveTravelerId = $em->getRepository('AppBundle:TravelerId')->findOneById($travelerId->getId());
            if( $travelerId->isOwnedByOrganization($this->getUser()->getOrganization()) and
                $liveTravelerId->isOwnedByOrganization($this->getUser()->getOrganization())
            ){
                $edit = $this->checkForTravelerIdEdit($liveTravelerId, $travelerId);
                $move = $this->checkForTravelerIdMovement($liveTravelerId, $travelerId);

                $travelerId = $em->merge($travelerId);
                if($travelerId->getTransform()){
                    $em->merge($travelerId->getTransform());
                }
                if($travelerId->getReverseTransform()){
                    $em->merge($travelerId->getReverseTransform());
                }

                $em->flush();
                if($edit){
                    $this->updateAclByRoles($edit, ['ROLE_USER'=>['view'], 'ROLE_ADMIN'=>'operator']);
                }
                if($move){
                    $this->updateAclByRoles($move, ['ROLE_USER'=>['view'], 'ROLE_ADMIN'=>'operator']);
                }
                return $travelerId;
            }else{
                throw $this->createAccessDeniedException();
            }
        }else{
            throw $this->createAccessDeniedException();
        }
    }

     /**
     * @Rest\Patch("/tid/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("travelerId", converter="fos_rest.request_body")
     */
    public function patchTravelerIdAction(\AppBundle\Entity\TravelerId $travelerId, $id)
    {
        if($this->get('security.authorization_checker')->isGranted('EDIT', $travelerId)){
            $em = $this->getDoctrine()->getManager();
            $em->detach($travelerId);
            $liveTravelerId = $em->getRepository('AppBundle:TravelerId')->findOneById($id);
            if($liveTravelerId->isOwnedByOrganization($this->getUser()->getOrganization())){
                $hasOrgUpdate = false;
                if( $travelerId->getInboundOrder() and
                    $travelerId->getInboundOrder()->getClient() and
                    $travelerId->getInboundOrder()->getClient()->getOrganization()
                ){
                    $hasOrgUpdate = true;
                }
                if( !$hasOrgUpdate or $travelerId->isOwnedByOrganization($this->getUser()->getOrganization()) ){
                    $edit = $this->checkForTravelerIdEdit($liveTravelerId, $travelerId);
                    $move = $this->checkForTravelerIdMovement($liveTravelerId, $travelerId);
                    $this->patchEntity($liveTravelerId, $travelerId);
                    $em->flush();
                    if($edit){
                        $this->updateAclByRoles($edit, ['ROLE_USER'=>['view'], 'ROLE_ADMIN'=>'operator']);
                    }
                    if($move){
                        $this->updateAclByRoles($move, ['ROLE_USER'=>['view'], 'ROLE_ADMIN'=>'operator']);
                    }
                    return $travelerId;
                }else{
                    throw $this->createAccessDeniedException();
                }
            }else{
                throw $this->createAccessDeniedException();
            }
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Delete("/tid/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function deleteTravelerIdAction(\AppBundle\Entity\TravelerId $travelerId)
    {
        if( $this->get('security.authorization_checker')->isGranted('DELETE', $travelerId) and
            $travelerId->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            $em = $this->getDoctrine()->getManager();
            $em->remove($travelerId);
            $em->flush();
            return $travelerId;
        }else{
            throw $this->createAccessDeniedException();
        }
    }


    /**
     * @Rest\Post("/mass_tid")
     * @Rest\View(template=":default:create_travelerid.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("massTravelerId", converter="fos_rest.request_body")
     */
    public function createMassTravelerIdAction(\AppBundle\Entity\MassTravelerId $massTravelerId)
    {
dump($massTravelerId);
        set_time_limit(300);
        ini_set('memory_limit','1024M');
        $em = $this->getDoctrine()->getManager();
        $createdEntities = [];
        foreach($massTravelerId->getTravelerIds() as $travelerId){
            if( $this->get('security.authorization_checker')->isGranted('CREATE', $travelerId) and
                $travelerId->isOwnedByOrganization($this->getUser()->getOrganization())
            ){
                $em->persist($travelerId);
                $travelerId->generateLabel();
                $createdEntities[] = $travelerId;
                $createdEntities = array_merge($createdEntities, $this->container->get('app.tid_init')->initialize($travelerId));
            }else{
                throw $this->createAccessDeniedException();
            }
        }
        $em->flush();
        foreach($createdEntities as $entity){
            $this->updateAclByRoles($entity, ['ROLE_USER'=>['view', 'edit'], 'ROLE_ADMIN'=>'operator']);
        }
        return $massTravelerId;
    }

    /**
     * @Rest\Put("/mass_tid/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("massTravelerId", converter="fos_rest.request_body")
     */
    public function updateMassTravelerIdAction(\AppBundle\Entity\MassTravelerId $massTravelerId)
    {
        set_time_limit(300);
        ini_set('memory_limit','1024M');
        $em = $this->getDoctrine()->getManager();
        $travelerIdLogEntities = [];
        $transformEntities = [];

        foreach($massTravelerId->getTravelerIds() as $travelerId){
            if($this->get('security.authorization_checker')->isGranted('EDIT', $travelerId)){
                $em->detach($travelerId);
                $liveTravelerId = $em->getRepository('AppBundle:TravelerId')->findOneById($travelerId->getId());
                if(!$liveTravelerId->isOwnedByOrganization($this->getUser()->getOrganization())){
                    throw $this->createAccessDeniedException();
                }
            }else{
                throw $this->createAccessDeniedException();
            }
        }

        if($massTravelerId->isTransform()){
            foreach($massTravelerId->getTravelerIds() as $travelerId){
                list($newTransformEntities, $transform, $mergedTravelerId) = $this->createTransformEntities($travelerId);
                if(!in_array($transform, $travelerIdLogEntities)){
                    $travelerIdLogEntities[] = $transform;
                }
                foreach($newTransformEntities as $newTransformEntity){
                    if(!in_array($newTransformEntity, $transformEntities)){
                        $transformEntities[] = $newTransformEntity;
                    }
                }
                $massTravelerId->getTravelerIds()->removeElement($travelerId);
                $massTravelerId->getTravelerIds()->add($mergedTravelerId);
            }
        }else{
            foreach($massTravelerId->getTravelerIds() as $travelerId){
                $edit = $this->checkForTravelerIdEdit($liveTravelerId, $travelerId);
                $move = $this->checkForTravelerIdMovement($liveTravelerId, $travelerId);
                if($edit){
                    $travelerIdLogEntities[] = $edit;
                }
                if($move){
                    $travelerIdLogEntities[] = $move;
                }
                $massTravelerId->getTravelerIds()->removeElement($travelerId);
                $mergedTravelerId = $em->merge($travelerId);
                $massTravelerId->getTravelerIds()->add($mergedTravelerId);
            }
        }

        foreach($massTravelerId->getTravelerIds() as $travelerId){
            if(!$travelerId->isOwnedByOrganization($this->getUser()->getOrganization())){
                throw $this->createAccessDeniedException();
            }
        }

        $em->flush();


        foreach($travelerIdLogEntities as $logEntity){
            $this->updateAclByRoles($logEntity, ['ROLE_USER'=>['view'], 'ROLE_ADMIN'=>'operator']);
        }

        foreach($transformEntities as $logEntity){
            $this->updateAclByRoles($logEntity, ['ROLE_USER'=>['view', 'edit'], 'ROLE_ADMIN'=>'operator']);
        }
        return $massTravelerId;
    }

    /**
     * @Rest\Get("/inventory_tid_edit")
     * @Rest\Get("/inventory_log")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function listInventoryTravelerIdEditAction(Request $request)
    {
        $page = (int)$request->query->get('page') - 1;
        $perPage =(int)$request->query->get('per_page');
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('COUNT(ite.id)')
            ->from('AppBundle:InventoryTravelerIdEdit', 'ite')
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
     * @Rest\Get("/inventory_tid_edit/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getInventoryTravelerIdEditAction(\AppBundle\Entity\InventoryTravelerIdEdit $inventoryTravelerIdEdit)
    {
        if( $this->get('security.authorization_checker')->isGranted('VIEW', $inventoryTravelerIdEdit) and
            $inventoryTravelerIdEdit->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            return $inventoryTravelerIdEdit;
        }else{
            throw $this->createNotFoundException('InventoryTravelerIdEdit #'.$inventoryTravelerIdEdit->getId().' Not Found');
        }
    }


    /**
     * @Rest\Get("/inventory_tid_movement")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function listInventoryTravelerIdMovementAction(Request $request)
    {
        $page = (int)$request->query->get('page') - 1;
        $perPage =(int)$request->query->get('per_page');
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('COUNT(itm.id)')
            ->from('AppBundle:InventoryTravelerIdMovement', 'itm')
            ->join('itm.travelerId', 'tid')
            ->join('tid.inboundOrder', 'o')
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
     * @Rest\Get("/inventory_tid_movement/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getInventoryTravelerIdMovementAction(\AppBundle\Entity\InventoryTravelerIdMovement $inventoryTravelerIdMovement)
    {
        if( $this->get('security.authorization_checker')->isGranted('VIEW', $inventoryTravelerIdMovement) and
            $inventoryTravelerIdMovement->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            return $inventoryTravelerIdMovement;
        }else{
            throw $this->createNotFoundException('InventoryTravelerIdMovement #'.$inventoryTravelerIdMovement->getId().' Not Found');
        }
    }

    /**
     * @Rest\Get("/inventory_tid_transform")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function listInventoryTravelerIdTransformAction(Request $request)
    {
        $page = (int)$request->query->get('page') - 1;
        $perPage =(int)$request->query->get('per_page');
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('COUNT(DISTINCT itt.id)')
            ->from('AppBundle:InventoryTravelerIdTransform', 'itt')
            ->join('itt.fromTravelerIds', 'tid')
            ->join('tid.inboundOrder', 'io')
            ->join('io.client', 'c')
            ->where('c.organization = :org')
            ->setParameter('org', $this->getUser()->getOrganization());

        $totalItems = $qb->getQuery()->getSingleScalarResult();

        Utilities::setupSearchableEntityQueryBuild($qb, $request);

        $totalCount = $qb->getQuery()->getSingleScalarResult();

        $qb->select('itt')
            ->orderBy('itt.id', 'DESC')
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
     * @Rest\Get("/inventory_tid_transform/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getInventoryTravelerIdTransformAction(\AppBundle\Entity\InventoryTravelerIdTransform $inventoryTravelerIdTransform)
    {
        if( $this->get('security.authorization_checker')->isGranted('VIEW', $inventoryTravelerIdTransform) and
            $inventoryTravelerIdTransform->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            return $inventoryTravelerIdTransform;
        }else{
            throw $this->createNotFoundException('InventoryTravelerIdTransform #'.$inventoryTravelerIdTransform->getId().' Not Found');
        }
    }

     /**
     * @Rest\Get("/inventory_tid_transform/{id}/void")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function voidInventoryTravelerIdTransformAction(\AppBundle\Entity\InventoryTravelerIdTransform $inventoryTravelerIdTransform)
    {
        if( $this->get('security.authorization_checker')->isGranted('VIEW', $inventoryTravelerIdTransform) and
            $inventoryTravelerIdTransform->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            $inventoryTravelerIdTransform->setIsVoid(true);
            return $inventoryTravelerIdTransform;
        }else{
            throw $this->createNotFoundException('InventoryTravelerIdTransform #'.$inventoryTravelerIdTransform->getId().' Not Found');
        }
    }

     /**
     * @Rest\Get("/inventory_tid_transform/{id}/restore")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function restoreInventoryTravelerIdTransformAction(\AppBundle\Entity\InventoryTravelerIdTransform $inventoryTravelerIdTransform)
    {
        if( $this->get('security.authorization_checker')->isGranted('VIEW', $inventoryTravelerIdTransform) and
            $inventoryTravelerIdTransform->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            $inventoryTravelerIdTransform->setIsVoid(false);
            return $inventoryTravelerIdTransform;
        }else{
            throw $this->createNotFoundException('InventoryTravelerIdTransform #'.$inventoryTravelerIdTransform->getId().' Not Found');
        }
    }

    /**
     * @Rest\Get("/unit")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function listUnitAction(Request $request)
    {
        $page = (int)$request->query->get('page') - 1;
        $perPage =(int)$request->query->get('per_page');
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('COUNT(u.id)')
            ->from('AppBundle:Unit', 'u')
            ->where('u.organization = :org')
            ->setParameter('org', $this->getUser()->getOrganization());

        $totalItems = $qb->getQuery()->getSingleScalarResult();

        Utilities::setupSearchableEntityQueryBuild($qb, $request);

        $totalCount = $qb->getQuery()->getSingleScalarResult();

        $qb->select('u')
            ->orderBy('u.id', 'DESC')
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
     * @Rest\Put("/unit/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("unit", converter="fos_rest.request_body")
     */
    public function updateUnitAction(\AppBundle\Entity\Unit $unit)
    {

        if($this->get('security.authorization_checker')->isGranted('EDIT', $unit)){
            $em = $this->getDoctrine()->getManager();
            $em->detach($unit);

            $liveUnit = $em->getRepository('AppBundle:Unit')->findOneById($unit->getId());
            if( $unit->isOwnedByOrganization($this->getUser()->getOrganization()) and
                $liveUnit->isOwnedByOrganization($this->getUser()->getOrganization())
            ){
                $unit = $em->merge($unit);
                foreach($unit->getProperties() as $property){
                    $property->setUnit($unit);
                }
                $em->flush();
                return $unit;
            }else{
                throw $this->createAccessDeniedException();
            }
        }else{
            throw $this->createAccessDeniedException();
        }
    }

}