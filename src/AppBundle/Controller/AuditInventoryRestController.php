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

        return ['total_count'=> (int)$totalCount, 'total_items' => (int)$totalItems, 'list'=>$items];
    }

    /**
     * @Rest\Get("/inventory_audit/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getInventoryAuditAction(\AppBundle\Entity\InventoryAudit $inventoryAudit)
    {
        return $inventoryAudit;
    }

    /**
     * @Rest\Post("/inventory_audit")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("inventoryAudit", converter="fos_rest.request_body")
     */
    public function createInventoryAuditAction(\AppBundle\Entity\InventoryAudit $inventoryAudit)
    {
        $em = $this->getDoctrine()->getManager();
        $inventoryAudit->setByUser($this->getUser());
        $inventoryAudit->setStartedAt(new \DateTime());
        $em->persist($inventoryAudit);

        $em->flush();
        return $inventoryAudit;
    }

     /**
     * @Rest\Put("/inventory_audit/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("inventoryAudit", converter="fos_rest.request_body")
     */
    public function updateInventoryAuditAction(\AppBundle\Entity\InventoryAudit $inventoryAudit)
    {
        $em = $this->getDoctrine()->getManager();
        $em->merge($inventoryAudit);
        if($inventoryAudit->getEndedAt()){
            $inventoryAudit->end();
        }
        $em->flush();
        return $inventoryAudit;
    }

    /**
     * @Rest\Post("/inventory_part_audit")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("inventoryPartAudit", converter="fos_rest.request_body")
     */
    public function createInventoryPartAuditAction(\AppBundle\Entity\InventoryPartAudit $inventoryPartAudit)
    {
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
        return $inventoryPartAudit;
    }

     /**
     * @Rest\Put("/inventory_part_audit/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("inventoryPartAudit", converter="fos_rest.request_body")
     */
    public function updateInventoryPartAuditAction(\AppBundle\Entity\InventoryPartAudit $inventoryPartAudit)
    {
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
    }

    /**
     * @Rest\Delete("/inventory_part_audit/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function deleteInventoryPartAuditAction(\AppBundle\Entity\InventoryPartAudit $inventoryPartAudit)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($inventoryPartAudit);
        $em->flush();
        return $inventoryPartAudit;
    }

}