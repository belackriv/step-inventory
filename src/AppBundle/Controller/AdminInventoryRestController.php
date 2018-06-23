<?php

namespace AppBundle\Controller;

use AppBundle\Library\Utilities;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\RestBundle\Controller\Annotations AS Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Collections\ArrayCollection;


class AdminInventoryRestController extends FOSRestController
{

    use Mixin\RestPatchMixin;
    use Mixin\UpdateAclMixin;
    use Mixin\WampUpdatePusher;

     /**
     * @Rest\Get("/sku")
     * @Rest\Get("/admin_inventory")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function listSkuAction(Request $request)
    {
        $page = (int)$request->query->get('page') - 1;
        $perPage =(int)$request->query->get('per_page');
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('COUNT(s.id)')
            ->from('AppBundle:Sku', 's')
            ->where('s.organization = :org')
            ->setParameter('org', $this->getUser()->getOrganization());

        $totalItems = $qb->getQuery()->getSingleScalarResult();

        Utilities::setupSearchableEntityQueryBuild($qb, $request);

        $totalCount = $qb->getQuery()->getSingleScalarResult();

        $qb->select('s')
            ->orderBy('s.id', 'DESC')
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
     * @Rest\Get("/sku/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getSkuAction(\AppBundle\Entity\Sku $sku)
    {
        if( $this->get('security.authorization_checker')->isGranted('VIEW', $sku) and
            $sku->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            return $sku;
        }else{
            throw $this->createNotFoundException('Sku #'.$sku->getId().' Not Found');
        }
    }

    /**
     * @Rest\Post("/sku")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("sku", converter="fos_rest.request_body")
     */
    public function createSkuAction(\AppBundle\Entity\Sku $sku)
    {
        if($this->get('security.authorization_checker')->isGranted('CREATE', $sku)){
            $em = $this->getDoctrine()->getManager();
            $sku->setOrganization($this->getUser()->getOrganization());
            $em->persist($sku);
            $em->flush();
            $this->updateAclByRoles($sku, ['ROLE_USER'=>'view', 'ROLE_ADMIN'=>'operator']);
            return $sku;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Put("/sku/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("sku", converter="fos_rest.request_body")
     */
    public function updateSkuAction(\AppBundle\Entity\Sku $sku)
    {
        if($this->get('security.authorization_checker')->isGranted('EDIT', $sku)){
            $em = $this->getDoctrine()->getManager();
            $em->detach($sku);
            $liveSku = $this->getDoctrine()->getRepository('AppBundle:Sku')->findOneById($sku->getId());
            if( $sku->isOwnedByOrganization($this->getUser()->getOrganization()) and
                $liveSku->isOwnedByOrganization($this->getUser()->getOrganization())
            ){
                $em->merge($sku);
                $em->flush();
                return $sku;
            }else{
                throw $this->createAccessDeniedException();
            }
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Delete("/sku/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function deleteSkuAction(\AppBundle\Entity\Sku $sku)
    {
        if( $this->get('security.authorization_checker')->isGranted('DELETE', $sku) and
            $sku->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            $em = $this->getDoctrine()->getManager();
            $tidUsingSku = $em->getRepository(\AppBundle\Entity\TravelerId::class)->findOneBy([
                'sku' => $sku
            ]);
            if($tidUsingSku){
                throw new \Exception('This Sku is associated with a TravelerId and cannot be deleted.  You can disabled the SKU instead.');
            }
            $em->remove($sku);
            $em->flush();
            return $sku;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Get("/part")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function listPartAction(Request $request)
    {
        $page = (int)$request->query->get('page') - 1;
        $perPage =(int)$request->query->get('per_page');
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('COUNT(p.id)')
            ->from('AppBundle:Part', 'p')
            ->where('p.organization = :org')
            ->setParameter('org', $this->getUser()->getOrganization());

        $totalItems = $qb->getQuery()->getSingleScalarResult();

        Utilities::setupSearchableEntityQueryBuild($qb, $request);

        $totalCount = $qb->getQuery()->getSingleScalarResult();

        $qb->select('p')
            ->orderBy('p.id', 'DESC')
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
     * @Rest\Get("/part/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getPartAction(\AppBundle\Entity\Part $part)
    {
        if( $this->get('security.authorization_checker')->isGranted('VIEW', $part) and
            $part->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            return $part;
        }else{
            throw $this->createNotFoundException('Part #'.$part->getId().' Not Found');
        }
    }

    /**
     * @Rest\Post("/part")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("part", converter="fos_rest.request_body")
     */
    public function createPartAction(\AppBundle\Entity\Part $part)
    {
        if($this->get('security.authorization_checker')->isGranted('CREATE', $part)){
            $em = $this->getDoctrine()->getManager();
            $part->setOrganization($this->getUser()->getOrganization());
            $em->persist($part);
            $em->flush();
            $this->updateAclByRoles($part, ['ROLE_USER'=>'view', 'ROLE_ADMIN'=>'operator']);
            return $part;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Put("/part/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("part", converter="fos_rest.request_body")
     */
    public function updatePartAction(\AppBundle\Entity\Part $part)
    {
        if($this->get('security.authorization_checker')->isGranted('EDIT', $part)){
            $em = $this->getDoctrine()->getManager();
            $em->detach($part);
            $livePart = $this->getDoctrine()->getRepository('AppBundle:Part')->findOneById($part->getId());
            if( $part->isOwnedByOrganization($this->getUser()->getOrganization()) and
                $livePart->isOwnedByOrganization($this->getUser()->getOrganization())
            ){
                $em->merge($part);
                $em->flush();
                return $part;
            }else{
                throw $this->createAccessDeniedException();
            }
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Delete("/part/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function deletePartAction(\AppBundle\Entity\Part $part)
    {
        if( $this->get('security.authorization_checker')->isGranted('DELETE', $part) and
            $part->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            $em = $this->getDoctrine()->getManager();
            $em->remove($part);
            $em->flush();
            return $part;
        }else{
            throw $this->createAccessDeniedException();
        }
    }


    /**
     * @Rest\Get("/part_category")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function listPartCategoryAction(Request $request)
    {
        $page = (int)$request->query->get('page') - 1;
        $perPage =(int)$request->query->get('per_page');
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('COUNT(pc.id)')
            ->from('AppBundle:PartCategory', 'pc')
            ->where('pc.organization = :org')
            ->setParameter('org', $this->getUser()->getOrganization());

        $totalItems = $qb->getQuery()->getSingleScalarResult();

        Utilities::setupSearchableEntityQueryBuild($qb, $request);

        $totalCount = $qb->getQuery()->getSingleScalarResult();

        $qb->select('pc')
            ->orderBy('pc.id', 'DESC')
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
     * @Rest\Get("/part_category/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getPartCategoryAction(\AppBundle\Entity\PartCategory $partCategory)
    {
        if( $this->get('security.authorization_checker')->isGranted('VIEW', $partCategory) and
            $partCategory->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            return $partCategory;
        }else{
            throw $this->createNotFoundException('PartCategory #'.$partCategory->getId().' Not Found');
        }
    }

    /**
     * @Rest\Post("/part_category")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("partCategory", converter="fos_rest.request_body")
     */
    public function createPartCategoryAction(\AppBundle\Entity\PartCategory $partCategory)
    {
        if($this->get('security.authorization_checker')->isGranted('CREATE', $partCategory)){
            $em = $this->getDoctrine()->getManager();
            $partCategory->setOrganization($this->getUser()->getOrganization());
            $em->persist($partCategory);
            $em->flush();
            $this->updateAclByRoles($partCategory, ['ROLE_USER'=>'view', 'ROLE_ADMIN'=>'operator']);
            return $partCategory;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Put("/part_category/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("partCategory", converter="fos_rest.request_body")
     */
    public function updatePartCategoryAction(\AppBundle\Entity\PartCategory $partCategory)
    {
        if($this->get('security.authorization_checker')->isGranted('EDIT', $partCategory)){
            $em = $this->getDoctrine()->getManager();
            $em->detach($partCategory);
            $livePartCAtegory = $this->getDoctrine()->getRepository('AppBundle:PartCAtegory')->findOneById($partCategory->getId());
            if( $partCategory->isOwnedByOrganization($this->getUser()->getOrganization()) and
                $livePartCAtegory->isOwnedByOrganization($this->getUser()->getOrganization())
            ){
                $em = $this->getDoctrine()->getManager();
                $em->merge($partCategory);
                $em->flush();
                return $partCategory;
            }else{
                throw $this->createAccessDeniedException();
            }
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Delete("/part_category/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function deletePartCategoryAction(\AppBundle\Entity\PartCategory $partCategory)
    {
        if( $this->get('security.authorization_checker')->isGranted('DELETE', $partCategory) and
            $partCategory->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            $em = $this->getDoctrine()->getManager();
            $em->remove($partCategory);
            $em->flush();
            return $partCategory;
        }else{
            throw $this->createAccessDeniedException();
        }
    }


    /**
     * @Rest\Get("/part_group")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function listPartGroupAction(Request $request)
    {
        $page = (int)$request->query->get('page') - 1;
        $perPage =(int)$request->query->get('per_page');
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('COUNT(pg.id)')
            ->from('AppBundle:PartGroup', 'pg')
            ->where('pg.organization = :org')
            ->setParameter('org', $this->getUser()->getOrganization());;

        $totalItems = $qb->getQuery()->getSingleScalarResult();

        Utilities::setupSearchableEntityQueryBuild($qb, $request);

        $totalCount = $qb->getQuery()->getSingleScalarResult();

        $qb->select('pg')
            ->orderBy('pg.id', 'DESC')
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

        return ['total_count'=> (int)$totalCount, 'total_items' => (int)$totalItems, 'list'=>$items];
    }

    /**
     * @Rest\Get("/part_group/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getPartGroupAction(\AppBundle\Entity\PartGroup $partGroup)
    {
        if( $this->get('security.authorization_checker')->isGranted('VIEW', $partGroup) and
            $partGroup->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            return $partGroup;
        }else{
            throw $this->createNotFoundException('PartGroup #'.$partGroup->getId().' Not Found');
        }
    }

    /**
     * @Rest\Post("/part_group")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("partGroup", converter="fos_rest.request_body")
     */
    public function createPartGroupAction(\AppBundle\Entity\PartGroup $partGroup)
    {
        if($this->get('security.authorization_checker')->isGranted('CREATE', $partGroup)){
            $em = $this->getDoctrine()->getManager();
            $partGroup->setOrganization($this->getUser()->getOrganization());
            $em->persist($partGroup);
            $em->flush();
            $this->updateAclByRoles($partGroup, ['ROLE_USER'=>'view', 'ROLE_ADMIN'=>'operator']);
            return $partGroup;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Put("/part_group/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("partGroup", converter="fos_rest.request_body")
     */
    public function updatePartGroupAction(\AppBundle\Entity\PartGroup $partGroup)
    {
        if($this->get('security.authorization_checker')->isGranted('EDIT', $partGroup)){
            $em = $this->getDoctrine()->getManager();
            $em->detach($partGroup);
            $livePartGroup = $this->getDoctrine()->getRepository('AppBundle:PartGroup')->findOneById($partGroup->getId());
            if( $partGroup->isOwnedByOrganization($this->getUser()->getOrganization()) and
                $livePartGroup->isOwnedByOrganization($this->getUser()->getOrganization())
            ){
                $em->merge($partGroup);
                $em->flush();
                return $partGroup;
            }else{
                throw $this->createAccessDeniedException();
            }
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Delete("/part_group/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function deletePartGroupAction(\AppBundle\Entity\PartGroup $partGroup)
    {
        if( $this->get('security.authorization_checker')->isGranted('DELETE', $partGroup) and
            $partGroup->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            $em = $this->getDoctrine()->getManager();
            $em->remove($partGroup);
            $em->flush();
            return $partGroup;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Get("/commodity")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function listCommodityAction(Request $request)
    {
        $page = (int)$request->query->get('page') - 1;
        $perPage =(int)$request->query->get('per_page');
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('COUNT(p.id)')
            ->from('AppBundle:Commodity', 'p')
            ->where('p.organization = :org')
            ->setParameter('org', $this->getUser()->getOrganization());

        $totalItems = $qb->getQuery()->getSingleScalarResult();

        Utilities::setupSearchableEntityQueryBuild($qb, $request);

        $totalCount = $qb->getQuery()->getSingleScalarResult();

        $qb->select('p')
            ->orderBy('p.id', 'DESC')
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
     * @Rest\Get("/commodity/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getCommodityAction(\AppBundle\Entity\Commodity $commodity)
    {
        if( $this->get('security.authorization_checker')->isGranted('VIEW', $commodity) and
            $commodity->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            return $commodity;
        }else{
            throw $this->createNotFoundException('Commodity #'.$commodity->getId().' Not Found');
        }
    }

    /**
     * @Rest\Post("/commodity")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("commodity", converter="fos_rest.request_body")
     */
    public function createCommodityAction(\AppBundle\Entity\Commodity $commodity)
    {
        if($this->get('security.authorization_checker')->isGranted('CREATE', $commodity)){
            $em = $this->getDoctrine()->getManager();
            $commodity->setOrganization($this->getUser()->getOrganization());
            $em->persist($commodity);
            $em->flush();
            $this->updateAclByRoles($commodity, ['ROLE_USER'=>'view', 'ROLE_ADMIN'=>'operator']);
            return $commodity;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Put("/commodity/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("commodity", converter="fos_rest.request_body")
     */
    public function updateCommodityAction(\AppBundle\Entity\Commodity $commodity)
    {
        if($this->get('security.authorization_checker')->isGranted('EDIT', $commodity)){
            $em = $this->getDoctrine()->getManager();
            $em->detach($commodity);
            $liveCommodity = $this->getDoctrine()->getRepository('AppBundle:Commodity')->findOneById($commodity->getId());
            if( $commodity->isOwnedByOrganization($this->getUser()->getOrganization()) and
                $liveCommodity->isOwnedByOrganization($this->getUser()->getOrganization())
            ){
                $em->merge($commodity);
                $em->flush();
                return $commodity;
            }else{
                throw $this->createAccessDeniedException();
            }
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Delete("/commodity/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function deleteCommodityAction(\AppBundle\Entity\Commodity $commodity)
    {
        if( $this->get('security.authorization_checker')->isGranted('DELETE', $commodity) and
            $commodity->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            $em = $this->getDoctrine()->getManager();
            $em->remove($commodity);
            $em->flush();
            return $commodity;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Get("/unit_type")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function listUnitTypeAction(Request $request)
    {
        $page = (int)$request->query->get('page') - 1;
        $perPage =(int)$request->query->get('per_page');
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('COUNT(ut.id)')
            ->from('AppBundle:UnitType', 'ut')
            ->where('ut.organization = :org')
            ->setParameter('org', $this->getUser()->getOrganization());

        $totalItems = $qb->getQuery()->getSingleScalarResult();

        Utilities::setupSearchableEntityQueryBuild($qb, $request);

        $totalCount = $qb->getQuery()->getSingleScalarResult();

        $qb->select('ut')
            ->orderBy('ut.id', 'DESC')
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
     * @Rest\Get("/unit_type/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getUnitTypeAction(\AppBundle\Entity\UnitType $unitType)
    {
        if( $this->get('security.authorization_checker')->isGranted('VIEW', $unitType) and
            $unitType->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            return $unitType;
        }else{
            throw $this->createNotFoundException('UnitType #'.$unitType->getId().' Not Found');
        }
    }

    /**
     * @Rest\Post("/unit_type")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("unitType", converter="fos_rest.request_body")
     */
    public function createUnitTypeAction(\AppBundle\Entity\UnitType $unitType)
    {
        if($this->get('security.authorization_checker')->isGranted('CREATE', $unitType)){
            $em = $this->getDoctrine()->getManager();
            $unitType->setOrganization($this->getUser()->getOrganization());
            $em->persist($unitType);
            $em->flush();
            $this->updateAclByRoles($unitType, ['ROLE_USER'=>'view', 'ROLE_ADMIN'=>'operator']);
            return $unitType;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Put("/unit_type/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("unitType", converter="fos_rest.request_body")
     */
    public function updateUnitTypeAction(\AppBundle\Entity\UnitType $unitType)
    {
        if($this->get('security.authorization_checker')->isGranted('EDIT', $unitType)){
            $em = $this->getDoctrine()->getManager();
            $em->detach($unitType);
            $liveUnitType = $this->getDoctrine()->getRepository('AppBundle:UnitType')->findOneById($unitType->getId());
            if( $unitType->isOwnedByOrganization($this->getUser()->getOrganization()) and
                $liveUnitType->isOwnedByOrganization($this->getUser()->getOrganization())
            ){
                $em->merge($unitType);
                $em->flush();
                return $unitType;
            }else{
                throw $this->createAccessDeniedException();
            }
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Delete("/unit_type/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function deleteUnitTypeAction(\AppBundle\Entity\UnitType $unitType)
    {
        if( $this->get('security.authorization_checker')->isGranted('DELETE', $unitType) and
            $unitType->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            $em = $this->getDoctrine()->getManager();
            $em->remove($unitType);
            $em->flush();
            return $unitType;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Post("/unit_type_property")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("unitTypeProperty", converter="fos_rest.request_body")
     */
    public function createUnitTypePropertyAction(\AppBundle\Entity\UnitTypeProperty $unitTypeProperty)
    {
        if($this->get('security.authorization_checker')->isGranted('CREATE', $unitTypeProperty)){
            $em = $this->getDoctrine()->getManager();
            $em->persist($unitTypeProperty);
            foreach($unitTypeProperty->getValidValues() as $validValue) {
                $validValue->setUnitTypeProperty($unitTypeProperty);
            }
            $em->flush();
            $this->updateAclByRoles($unitTypeProperty, ['ROLE_USER'=>'view', 'ROLE_ADMIN'=>'operator']);
            return $unitTypeProperty;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Put("/unit_type_property/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("unitTypeProperty", converter="fos_rest.request_body")
     */
    public function updateUnitTypePropertyAction(\AppBundle\Entity\UnitTypeProperty $unitTypeProperty)
    {
        if($this->get('security.authorization_checker')->isGranted('EDIT', $unitTypeProperty)){
            $em = $this->getDoctrine()->getManager();
            $em->detach($unitTypeProperty);
            $liveUnitTypeProperty = $this->getDoctrine()->getRepository('AppBundle:UnitTypeProperty')->findOneById($unitTypeProperty->getId());
            if( $unitTypeProperty->isOwnedByOrganization($this->getUser()->getOrganization()) and
                $liveUnitTypeProperty->isOwnedByOrganization($this->getUser()->getOrganization())
            ){
                $unitTypeProperty = $em->merge($unitTypeProperty);
                foreach($unitTypeProperty->getValidValues() as $validValue) {
                    $validValue->setUnitTypeProperty($unitTypeProperty);
                }
                $em->flush();
                return $unitTypeProperty;
            }else{
                throw $this->createAccessDeniedException();
            }
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Delete("/unit_type_property/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function deleteUnitTypePropertyAction(\AppBundle\Entity\UnitTypeProperty $unitTypeProperty)
    {
        if( $this->get('security.authorization_checker')->isGranted('DELETE', $unitTypeProperty) and
            $unitTypeProperty->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            $em = $this->getDoctrine()->getManager();
            $em->remove($unitTypeProperty);
            $em->flush();
            return $unitTypeProperty;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Delete("/unit_type_property_valid_value/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function deleteUnitTypePropertyValidValueAction(\AppBundle\Entity\UnitTypePropertyValidValue $unitTypePropertyValidValue)
    {
        if( $this->get('security.authorization_checker')->isGranted('DELETE', $unitTypePropertyValidValue->getUnitTypeProperty()) and
            $unitTypePropertyValidValue->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            $em = $this->getDoctrine()->getManager();
            $em->remove($unitTypePropertyValidValue);
            $em->flush();
            return $unitTypePropertyValidValue;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Get("/bin_type")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function listBinTypeAction(Request $request)
    {
        $page = (int)$request->query->get('page') - 1;
        $perPage =(int)$request->query->get('per_page');
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('COUNT(bt.id)')
            ->from('AppBundle:BinType', 'bt')
            ->where('bt.organization = :org')
            ->setParameter('org', $this->getUser()->getOrganization());

        $totalItems = $qb->getQuery()->getSingleScalarResult();

        Utilities::setupSearchableEntityQueryBuild($qb, $request);

        $totalCount = $qb->getQuery()->getSingleScalarResult();

        $qb->select('bt')
            ->orderBy('bt.id', 'DESC')
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
     * @Rest\Get("/bin_type/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getBinTypeAction(\AppBundle\Entity\BinType $binType)
    {
        if( $this->get('security.authorization_checker')->isGranted('VIEW', $binType) and
            $binType->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            return $binType;
        }else{
            throw $this->createNotFoundException('BinType #'.$binType->getId().' Not Found');
        }
    }

    /**
     * @Rest\Post("/bin_type")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("binType", converter="fos_rest.request_body")
     */
    public function createBinTypeAction(\AppBundle\Entity\BinType $binType)
    {
        if($this->get('security.authorization_checker')->isGranted('CREATE', $binType)){
            $em = $this->getDoctrine()->getManager();
            $binType->setOrganization($this->getUser()->getOrganization());
            $em->persist($binType);
            $em->flush();
            $this->updateAclByRoles($binType, ['ROLE_USER'=>'view', 'ROLE_ADMIN'=>'operator']);
            return $binType;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Put("/bin_type/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("binType", converter="fos_rest.request_body")
     */
    public function updateBinTypeAction(\AppBundle\Entity\BinType $binType)
    {
        if($this->get('security.authorization_checker')->isGranted('EDIT', $binType)){
            $em = $this->getDoctrine()->getManager();
            $em->detach($binType);
            $liveBinType = $this->getDoctrine()->getRepository('AppBundle:BinType')->findOneById($binType->getId());
            if( $binType->isOwnedByOrganization($this->getUser()->getOrganization()) and
                $liveBinType->isOwnedByOrganization($this->getUser()->getOrganization())
            ){
                $em->merge($binType);
                $em->flush();
                return $binType;
            }else{
                throw $this->createAccessDeniedException();
            }
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Delete("/bin_type/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function deleteBinTypeAction(\AppBundle\Entity\BinType $binType)
    {
        if( $this->get('security.authorization_checker')->isGranted('DELETE', $binType) and
            $binType->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            $em = $this->getDoctrine()->getManager();
            $em->remove($binType);
            $em->flush();
            return $binType;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Get("/bin")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function listBinAction(Request $request)
    {
        $page = (int)$request->query->get('page') - 1;
        $perPage =(int)$request->query->get('per_page');
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('COUNT(b.id)')
            ->from('AppBundle:Bin', 'b')
            ->join('b.department', 'd')
            ->join('d.office', 'o')
            ->where('o.organization = :org')
            ->setParameter('org', $this->getUser()->getOrganization());

        $totalItems = $qb->getQuery()->getSingleScalarResult();

        Utilities::setupSearchableEntityQueryBuild($qb, $request);

        $totalCount = $qb->getQuery()->getSingleScalarResult();

        $qb->select('b')
            ->orderBy('b.id', 'DESC')
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
     * @Rest\Get("/bin/{id}")
     * @Rest\Get("/show/bin/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default","Bin"})
     */
    public function getBinAction(\AppBundle\Entity\Bin $bin)
    {
        if( $this->get('security.authorization_checker')->isGranted('VIEW', $bin) and
            $bin->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            return $bin;
        }else{
            throw $this->createNotFoundException('Bin #'.$bin->getId().' Not Found');
        }
    }

    /**
     * @Rest\Post("/bin")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("bin", converter="fos_rest.request_body")
     */
    public function createBinAction(\AppBundle\Entity\Bin $bin)
    {
        if( $this->get('security.authorization_checker')->isGranted('CREATE', $bin) and
            $bin->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            $em = $this->getDoctrine()->getManager();
            $em->persist($bin);
            $em->flush();
            $this->updateAclByRoles($bin, ['ROLE_USER'=>'view', 'ROLE_ADMIN'=>'operator']);
            return $bin;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Put("/bin/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("bin", converter="fos_rest.request_body")
     */
    public function updateBinAction(\AppBundle\Entity\Bin $bin)
    {
        if( $this->get('security.authorization_checker')->isGranted('EDIT', $bin)){
            $em = $this->getDoctrine()->getManager();
            $em->detach($bin);
            $liveBin = $this->getDoctrine()->getRepository('AppBundle:Bin')->findOneById($bin->getId());
            if( $bin->isOwnedByOrganization($this->getUser()->getOrganization()) and
                $liveBin->isOwnedByOrganization($this->getUser()->getOrganization())
            ){
                $em = $this->getDoctrine()->getManager();
                $em->merge($bin);
                $em->flush();
                return $bin;
            }else{
                throw $this->createAccessDeniedException();
            }
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Delete("/bin/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function deleteBinAction(\AppBundle\Entity\Bin $bin)
    {
        if( $this->get('security.authorization_checker')->isGranted('DELETE', $bin) and
            $bin->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            $em = $this->getDoctrine()->getManager();
            $em->remove($bin);
            $em->flush();
            return $bin;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Get("/inventory_movement_rule")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function listInventoryMovementRuleAction(Request $request)
    {
        $page = (int)$request->query->get('page') - 1;
        $perPage =(int)$request->query->get('per_page');
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('COUNT(imr.id)')
            ->from('AppBundle:InventoryMovementRule', 'imr');

        $totalItems = $qb->getQuery()->getSingleScalarResult();

        Utilities::setupSearchableEntityQueryBuild($qb, $request);

        $totalCount = $qb->getQuery()->getSingleScalarResult();

        $qb->select('imr')
            ->orderBy('imr.id', 'DESC')
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
     * @Rest\Get("/inventory_movement_rule/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getInventoryMovementRuleAction(\AppBundle\Entity\InventoryMovementRule $inventoryMovementRule)
    {
        if( $this->get('security.authorization_checker')->isGranted('VIEW', $inventoryMovementRule) and
            $inventoryMovementRule->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            return $inventoryMovementRule;
        }else{
            throw $this->createNotFoundException('InventoryMovementRule #'.$inventoryMovementRule->getId().' Not Found');
        }
    }

    /**
     * @Rest\Post("/inventory_movement_rule")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("inventoryMovementRule", converter="fos_rest.request_body")
     */
    public function createInventoryMovementRuleAction(\AppBundle\Entity\InventoryMovementRule $inventoryMovementRule)
    {
        if( $this->get('security.authorization_checker')->isGranted('CREATE', $inventoryMovementRule) and
            $inventoryMovementRule->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            $em = $this->getDoctrine()->getManager();
            $em->persist($inventoryMovementRule);
            $em->flush();
            $this->updateAclByRoles($inventoryMovementRule, ['ROLE_USER'=>'view', 'ROLE_ADMIN'=>'operator']);
            return $inventoryMovementRule;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Put("/inventory_movement_rule/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("inventoryMovementRule", converter="fos_rest.request_body")
     */
    public function updateInventoryMovementRuleAction(\AppBundle\Entity\InventoryMovementRule $inventoryMovementRule)
    {
        if($this->get('security.authorization_checker')->isGranted('EDIT', $inventoryMovementRule)){
            $em = $this->getDoctrine()->getManager();
            $em->detach($inventoryMovementRule);
            $liveInventoryMovementRule = $this->getDoctrine()->getRepository('AppBundle:InventoryMovementRule')->findOneById($inventoryMovementRule->getId());
            if( $inventoryMovementRule->isOwnedByOrganization($this->getUser()->getOrganization()) and
                $liveInventoryMovementRule->isOwnedByOrganization($this->getUser()->getOrganization())
            ){
                $em->merge($inventoryMovementRule);
                $em->flush();
                return $inventoryMovementRule;
            }else{
                throw $this->createAccessDeniedException();
            }
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Delete("/inventory_movement_rule/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function deleteInventoryMovementRuleAction(\AppBundle\Entity\InventoryMovementRule $inventoryMovementRule)
    {
        if( $this->get('security.authorization_checker')->isGranted('DELETE', $inventoryMovementRule) and
            $inventoryMovementRule->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            $em = $this->getDoctrine()->getManager();
            $em->remove($inventoryMovementRule);
            $em->flush();
            return $inventoryMovementRule;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Get("/inventory_alert")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function listInventoryAlertAction(Request $request)
    {
        $page = (int)$request->query->get('page') - 1;
        $perPage =(int)$request->query->get('per_page');
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('COUNT(ia.id)')
            ->from('AppBundle:InventoryAlert', 'ia')
            ->join('ia.department', 'd')
            ->join('d.office', 'o')
            ->where('o.organization = :org')
            ->setParameter('org', $this->getUser()->getOrganization());;

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
     * @Rest\Get("/inventory_alert/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getInventoryAlertAction(\AppBundle\Entity\InventoryAlert $inventoryAlert)
    {
        if( $this->get('security.authorization_checker')->isGranted('VIEW', $inventoryAlert) and
            $inventoryAlert->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            return $inventoryAlert;
        }else{
            throw $this->createNotFoundException('InventoryAlert #'.$inventoryAlert->getId().' Not Found');
        }
    }

    /**
     * @Rest\Post("/inventory_alert")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("inventoryAlert", converter="fos_rest.request_body")
     */
    public function createInventoryAlertAction(\AppBundle\Entity\InventoryAlert $inventoryAlert)
    {
        if( $this->get('security.authorization_checker')->isGranted('CREATE', $inventoryAlert) and
            $inventoryAlert->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            $em = $this->getDoctrine()->getManager();
            $em->persist($inventoryAlert);
            $em->flush();
            $this->updateAclByRoles($inventoryAlert, ['ROLE_USER'=>'view', 'ROLE_ADMIN'=>'operator']);
            return $inventoryAlert;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Put("/inventory_alert/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("inventoryAlert", converter="fos_rest.request_body")
     */
    public function updateInventoryAlertAction(\AppBundle\Entity\InventoryAlert $inventoryAlert)
    {
        if($this->get('security.authorization_checker')->isGranted('EDIT', $inventoryAlert)){
            $em = $this->getDoctrine()->getManager();
            $em->detach($inventoryAlert);
            $liveInventoryAlert = $this->getDoctrine()->getRepository('AppBundle:InventoryAlert')->findOneById($inventoryAlert->getId());
            if( $inventoryAlert->isOwnedByOrganization($this->getUser()->getOrganization()) and
                $liveInventoryAlert->isOwnedByOrganization($this->getUser()->getOrganization())
            ){
                $em->merge($inventoryAlert);
                $em->flush();
                return $inventoryAlert;
            }else{
                throw $this->createAccessDeniedException();
            }
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Delete("/inventory_alert/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function deleteInventoryAlertAction(\AppBundle\Entity\InventoryAlert $inventoryAlert)
    {
        if( $this->get('security.authorization_checker')->isGranted('DELETE', $inventoryAlert) and
            $inventoryAlert->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            $em = $this->getDoctrine()->getManager();
            throw new \Exception("You cannont delete inventory alerts, disable instead.");

            $em->remove($inventoryAlert);
            $em->flush();
            return $inventoryAlert;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Get("/inventory_alert/{id}/run")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function runInventoryAlertAction(\AppBundle\Entity\InventoryAlert $inventoryAlert)
    {
        if( $this->get('security.authorization_checker')->isGranted('VIEW', $inventoryAlert) and
            $inventoryAlert->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            $results = $this->container->get('app.inventory_alerts')->check($inventoryAlert);
            return $results;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Get("/inventory_alerts/run_all")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function runAllInventoryAlertsAction()
    {
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('ia)')
            ->from('AppBundle:InventoryAlert', 'ia')
            ->join('ia.department', 'd')
            ->join('d.office', 'o')
            ->where('o.organization = :org')
            ->andWhere('ia.isActive = :true')
            ->setParameter('org', $this->getUser()->getOrganization())
            ->setParameter('true', true);

        $items = $qb->getQuery()->getResult();

        $results = [
            'alertsRun' => 0,
            'alertsFound' => 0,
            'alertsSent' => 0,
            'alertLogs' => [],
        ];
        $authorizationChecker = $this->get('security.authorization_checker');
        foreach($items as $item){
            if (true === $authorizationChecker->isGranted('VIEW', $item)){
                $result = $this->container->get('app.inventory_alerts')->check($inventoryAlert);
                $results['alertsRun'] += $result['alertsRun'];
                $results['alertsFound'] += $result['alertsFound'];
                $results['alertsSent'] += $result['alertsSent'];
                $results['alertsLogs'] = array_merge($results['alertsLogs'], $result['alertsLogs']);
            }
        }
        return $results;
    }

    /**
     * @Rest\Get("/inventory_alert_log/{id}/dismiss")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function dismessInventoryAlertLogAction(\AppBundle\Entity\InventoryAlertLog $inventoryAlertLog)
    {
        if( $this->get('security.authorization_checker')->isGranted('EDIT', $inventoryAlertLog) and
            $inventoryAlertLog->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            $inventoryAlertLog->setIsActive(false);
            $this->getDoctrine()->getManager()->flush();
            return $inventoryAlertLog;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

}