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


class PartInventoryRestController extends FOSRestController
{

    use Mixin\RestPatchMixin;
    use Mixin\UpdateAclMixin;
    use Mixin\WampUpdatePusher;

    /**
     * @Rest\Get("/bin_part_count")
     * @Rest\Get("/inventory")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default","BinPartCount"})
     */
    public function listBinPartCountAction(Request $request)
    {
        $page = (int)$request->query->get('page') - 1;
        $perPage =(int)$request->query->get('per_page');
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('COUNT(bpc.id)')
            ->from('AppBundle:BinPartCount', 'bpc');

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
     * @Rest\Get("/bin_part_count/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getBinPartCountAction(\AppBundle\Entity\BinPartCount $binPartCount)
    {
        if($this->get('security.authorization_checker')->isGranted('VIEW', $binPartCount)){
            return $binPartCount;
        }else{
            throw $this->createNotFoundException('BinPartCount #'.$binPartCount->getId().' Not Found');
        }
    }

    /**
     * @Rest\Get("/inventory_part_adjustment")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function listInventoryPartAdjustmentAction(Request $request)
    {
        $page = (int)$request->query->get('page') - 1;
        $perPage =(int)$request->query->get('per_page');
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('COUNT(ipa.id)')
            ->from('AppBundle:InventoryPartAdjustment', 'ipa');

        $totalItems = $qb->getQuery()->getSingleScalarResult();

        Utilities::setupSearchableEntityQueryBuild($qb, $request);

        $totalCount = $qb->getQuery()->getSingleScalarResult();

        $qb->select('ipa')
            ->orderBy('ipa.id', 'DESC')
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
     * @Rest\Get("/inventory_part_adjustment/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getInventoryPartAdjustmentAction(\AppBundle\Entity\InventoryPartAdjustment $inventoryPartAdjustment)
    {
        if($this->get('security.authorization_checker')->isGranted('VIEW', $inventoryPartAdjustment)){
            return $inventoryPartAdjustment;
        }else{
            throw $this->createNotFoundException('InventoryPartAdjustment #'.$inventoryPartAdjustment->getId().' Not Found');
        }
    }

    /**
     * @Rest\Post("/inventory_part_adjustment")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("inventoryPartAdjustment", converter="fos_rest.request_body")
     */
    public function createInventoryPartAdjustmentAction(\AppBundle\Entity\InventoryPartAdjustment $inventoryPartAdjustment)
    {
        if($this->get('security.authorization_checker')->isGranted('CREATE', $inventoryPartAdjustment)){
            $em = $this->getDoctrine()->getManager();
            $inventoryPartAdjustment->setByUser($this->getUser());
            $inventoryPartAdjustment->setPerformedAt(new \DateTime());
            $em->persist($inventoryPartAdjustment);

            $binPartCount = $this->getDoctrine()->getRepository('AppBundle:BinPartCount')
                ->findOneBy([
                    'bin' => $inventoryPartAdjustment->getForBin(),
                    'part' => $inventoryPartAdjustment->getPart()
                ]);
            if(!$binPartCount){
                $binPartCount = new \AppBundle\Entity\BinPartCount();
                $binPartCount->setBin($inventoryPartAdjustment->getForBin());
                $binPartCount->setPart($inventoryPartAdjustment->getPart());
                $em->persist($binPartCount);
            }else{
                if($inventoryPartAdjustment->getOldCount() === null){
                    throw new HttpException(Response::HTTP_UNPROCESSABLE_ENTITY, 'Bin Count Found for Bin "'.$inventoryPartAdjustment->getForBin()->getName()
                        .'" and Part "'.$inventoryPartAdjustment->getPart()->getName()
                        .'".  Please "Adjust" Inventory instead of "Adding". ');
                }
                $inventoryPartAdjustment->setOldCount($binPartCount->getCount());
            }

            $binPartCount->setCount($inventoryPartAdjustment->getNewCount());

            $em->flush();
            return $inventoryPartAdjustment;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Get("/inventory_part_movement")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function listInventoryPartMovementAction(Request $request)
    {
        $page = (int)$request->query->get('page') - 1;
        $perPage =(int)$request->query->get('per_page');
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('COUNT(ipm.id)')
            ->from('AppBundle:InventoryPartMovement', 'ipm');

        $totalItems = $qb->getQuery()->getSingleScalarResult();

        Utilities::setupSearchableEntityQueryBuild($qb, $request);

        $totalCount = $qb->getQuery()->getSingleScalarResult();

        $qb->select('ipm')
            ->orderBy('ipm.id', 'DESC')
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
     * @Rest\Get("/inventory_part_movement/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getInventoryPartMovementAction(\AppBundle\Entity\InventoryPartMovement $inventoryPartMovement)
    {
        if($this->get('security.authorization_checker')->isGranted('VIEW', $inventoryPartMovement)){
            return $inventoryPartMovement;
        }else{
            throw $this->createNotFoundException('InventoryPartMovement #'.$inventoryPartMovement->getId().' Not Found');
        }
    }

    /**
     * @Rest\Post("/inventory_part_movement")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("inventoryPartMovement", converter="fos_rest.request_body")
     */
    public function createInventoryPartMovementAction(\AppBundle\Entity\InventoryPartMovement $inventoryPartMovement)
    {
        if($this->get('security.authorization_checker')->isGranted('CREATE', $inventoryPartAdjustment)){
            if($inventoryPartMovement->getCount() === null){
                throw new HttpException(Response::HTTP_UNPROCESSABLE_ENTITY, 'Count Must Be Set To Move Parts.');
            }

            $em = $this->getDoctrine()->getManager();
            $inventoryPartMovement->setByUser($this->getUser());
            $inventoryPartMovement->setMovedAt(new \DateTime());
            $em->persist($inventoryPartMovement);

            $fromBinPartCount = $this->getDoctrine()->getRepository('AppBundle:BinPartCount')
                ->findOneBy([
                    'bin' => $inventoryPartMovement->getFromBin(),
                    'part' => $inventoryPartMovement->getPart()
                ]);
            if(!$fromBinPartCount){
                throw new HttpException(Response::HTTP_NOT_FOUND, 'Bin Count Not Found for Bin "'.$inventoryPartMovement->getFromBin()->getName()
                        .'" and Part "'.$inventoryPartMovement->getPart()->getName().'".');
            }else{
                $fromBinPartCount->setCount( $fromBinPartCount->getCount() - $inventoryPartMovement->getCount());
            }


             $toBinPartCount = $this->getDoctrine()->getRepository('AppBundle:BinPartCount')
                ->findOneBy([
                    'bin' => $inventoryPartMovement->getToBin(),
                    'part' => $inventoryPartMovement->getPart()
                ]);
            if(!$toBinPartCount){
                $binPartCount = new \AppBundle\Entity\BinPartCount();
                $binPartCount->setBin($inventoryPartMovement->getToBin());
                $binPartCount->setPart($inventoryPartMovement->getPart());
                $binPartCount->setCount($inventoryPartMovement->getCount());
                $em->persist($binPartCount);
            }else{
                $toBinPartCount->setCount( $toBinPartCount->getCount() + $inventoryPartMovement->getCount());
            }

            $em->flush();
            return $inventoryPartMovement;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

}