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


class SkuInventoryRestController extends FOSRestController
{

    use Mixin\RestPatchMixin;
    use Mixin\UpdateAclMixin;
    use Mixin\WampUpdatePusher;

    /**
     * @Rest\Get("/bin_sku_count")
     * @Rest\Get("/inventory")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default","BinSkuCount"})
     */
    public function listBinSkuCountAction(Request $request)
    {
        $page = (int)$request->query->get('page') - 1;
        $perPage =(int)$request->query->get('per_page');
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('COUNT(bpc.id)')
            ->from('AppBundle:BinSkuCount', 'bpc')
            ->join('bpc.bin', 'b')
            ->join('b.department', 'd')
            ->join('d.office', 'o')
            ->where('o.organization = :org')
            ->setParameter('org', $this->getUser()->getOrganization());

        $totalItems = $qb->getQuery()->getSingleScalarResult();

        Utilities::setupSearchableEntityQueryBuild($qb, $request);

        $totalCount = $qb->getQuery()->getSingleScalarResult();

        $qb->select('bpc')
            ->orderBy('bpc.id', 'DESC')
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
     * @Rest\Get("/bin_sku_count/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getBinSkuCountAction(\AppBundle\Entity\BinSkuCount $binSkuCount)
    {
        if( $this->get('security.authorization_checker')->isGranted('VIEW', $binSkuCount) and
            $binSkuCount->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            return $binSkuCount;
        }else{
            throw $this->createNotFoundException('BinSkuCount #'.$binSkuCount->getId().' Not Found');
        }
    }

    /**
     * @Rest\Get("/inventory_sku_adjustment")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function listInventorySkuAdjustmentAction(Request $request)
    {
        $page = (int)$request->query->get('page') - 1;
        $perPage =(int)$request->query->get('per_page');
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('COUNT(isa.id)')
            ->from('AppBundle:InventorySkuAdjustment', 'isa')
            ->join('isa.sku', 's')
            ->where('s.organization = :org')
            ->setParameter('org', $this->getUser()->getOrganization());

        $totalItems = $qb->getQuery()->getSingleScalarResult();

        Utilities::setupSearchableEntityQueryBuild($qb, $request);

        $totalCount = $qb->getQuery()->getSingleScalarResult();

        $qb->select('isa')
            ->orderBy('isa.id', 'DESC')
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
     * @Rest\Get("/inventory_sku_adjustment/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getInventorySkuAdjustmentAction(\AppBundle\Entity\InventorySkuAdjustment $inventorySkuAdjustment)
    {
        if( $this->get('security.authorization_checker')->isGranted('VIEW', $inventorySkuAdjustment) and
            $inventorySkuAdjustment->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            return $inventorySkuAdjustment;
        }else{
            throw $this->createNotFoundException('InventorySkuAdjustment #'.$inventorySkuAdjustment->getId().' Not Found');
        }
    }

    /**
     * @Rest\Post("/inventory_sku_adjustment")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("inventorySkuAdjustment", converter="fos_rest.request_body")
     */
    public function createInventorySkuAdjustmentAction(\AppBundle\Entity\InventorySkuAdjustment $inventorySkuAdjustment)
    {
        $inventorySkuAdjustment->setByUser($this->getUser());
        if( $this->get('security.authorization_checker')->isGranted('CREATE', $inventorySkuAdjustment) and
            $inventorySkuAdjustment->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            $inventorySkuAdjustment->setPerformedAt(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($inventorySkuAdjustment);

            $binSkuCount = $this->getDoctrine()->getRepository('AppBundle:BinSkuCount')
                ->findOneBy([
                    'bin' => $inventorySkuAdjustment->getForBin(),
                    'sku' => $inventorySkuAdjustment->getSku()
                ]);
            if(!$binSkuCount){
                $binSkuCount = new \AppBundle\Entity\BinSkuCount();
                $binSkuCount->setBin($inventorySkuAdjustment->getForBin());
                $binSkuCount->setSku($inventorySkuAdjustment->getSku());
                $em->persist($binSkuCount);
            }else{
                if($inventorySkuAdjustment->getOldCount() === null){
                    throw new HttpException(Response::HTTP_UNPROCESSABLE_ENTITY, 'Bin Count Found for Bin "'.$inventorySkuAdjustment->getForBin()->getName()
                        .'" and Sku "'.$inventorySkuAdjustment->getSku()->getName()
                        .'".  Please "Adjust" Inventory instead of "Adding". ');
                }
                $inventorySkuAdjustment->setOldCount($binSkuCount->getCount());
            }

            $binSkuCount->setCount($inventorySkuAdjustment->getNewCount());

            $em->flush();

            $this->updateAclByRoles($inventorySkuAdjustment, ['ROLE_USER'=>['view', 'edit'], 'ROLE_ADMIN'=>'operator']);
            $this->updateAclByRoles($binSkuCount, ['ROLE_USER'=>['view', 'edit'], 'ROLE_ADMIN'=>'operator']);
            return $inventorySkuAdjustment;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Get("/inventory_sku_movement")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function listInventorySkuMovementAction(Request $request)
    {
        $page = (int)$request->query->get('page') - 1;
        $perPage =(int)$request->query->get('per_page');
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('COUNT(isu.id)')
            ->from('AppBundle:InventorySkuMovement', 'isu')
            ->join('isu.sku', 's')
            ->where('s.organization = :org')
            ->setParameter('org', $this->getUser()->getOrganization());;

        $totalItems = $qb->getQuery()->getSingleScalarResult();

        Utilities::setupSearchableEntityQueryBuild($qb, $request);

        $totalCount = $qb->getQuery()->getSingleScalarResult();

        $qb->select('isu')
            ->orderBy('isu.id', 'DESC')
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
     * @Rest\Get("/inventory_sku_movement/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getInventorySkuMovementAction(\AppBundle\Entity\InventorySkuMovement $inventorySkuMovement)
    {
        if( $this->get('security.authorization_checker')->isGranted('VIEW', $inventorySkuMovement) and
            $inventorySkuMovement->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            return $inventorySkuMovement;
        }else{
            throw $this->createNotFoundException('InventorySkuMovement #'.$inventorySkuMovement->getId().' Not Found');
        }
    }

    /**
     * @Rest\Post("/inventory_sku_movement")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("inventorySkuMovement", converter="fos_rest.request_body")
     */
    public function createInventorySkuMovementAction(\AppBundle\Entity\InventorySkuMovement $inventorySkuMovement)
    {
        $inventorySkuMovement->setByUser($this->getUser());
        if( $this->get('security.authorization_checker')->isGranted('CREATE', $inventorySkuMovement) and
            $inventorySkuMovement->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            if($inventorySkuMovement->getCount() === null){
                throw new HttpException(Response::HTTP_UNPROCESSABLE_ENTITY, 'Count Must Be Set To Move Skus.');
            }
            $inventorySkuMovement->setMovedAt(new \DateTime());
            $em = $this->getDoctrine()->getManager();
            $em->persist($inventorySkuMovement);

            $fromBinSkuCount = $this->getDoctrine()->getRepository('AppBundle:BinSkuCount')
                ->findOneBy([
                    'bin' => $inventorySkuMovement->getFromBin(),
                    'sku' => $inventorySkuMovement->getSku()
                ]);
            if(!$fromBinSkuCount){
                throw new HttpException(Response::HTTP_NOT_FOUND, 'Bin Count Not Found for Bin "'.$inventorySkuMovement->getFromBin()->getName()
                        .'" and Sku "'.$inventorySkuMovement->getSku()->getName().'".');
            }else{
                $fromBinSkuCount->setCount( $fromBinSkuCount->getCount() - $inventorySkuMovement->getCount());
            }


             $toBinSkuCount = $this->getDoctrine()->getRepository('AppBundle:BinSkuCount')
                ->findOneBy([
                    'bin' => $inventorySkuMovement->getToBin(),
                    'sku' => $inventorySkuMovement->getSku()
                ]);
            if(!$toBinSkuCount){
                $toBinSkuCount = new \AppBundle\Entity\BinSkuCount();
                $toBinSkuCount->setBin($inventorySkuMovement->getToBin());
                $toBinSkuCount->setSku($inventorySkuMovement->getSku());
                $toBinSkuCount->setCount($inventorySkuMovement->getCount());
                $em->persist($toBinSkuCount);
            }else{
                $toBinSkuCount->setCount( $toBinSkuCount->getCount() + $inventorySkuMovement->getCount());
            }

            $em->flush();
            $this->updateAclByRoles($inventorySkuMovement, ['ROLE_USER'=>['view', 'edit'], 'ROLE_ADMIN'=>'operator']);
            $this->updateAclByRoles($toBinSkuCount, ['ROLE_USER'=>['view', 'edit'], 'ROLE_ADMIN'=>'operator']);
            return $inventorySkuMovement;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

}