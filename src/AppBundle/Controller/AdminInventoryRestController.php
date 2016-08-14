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
    use Mixin\WampUpdatePusher;


    /**
     * @Rest\Get("/part")
     * @Rest\Get("/admin_inventory")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function listPartAction(Request $request)
    {
        $page = (int)$request->query->get('page') - 1;
        $perPage =(int)$request->query->get('per_page');
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('COUNT(p.id)')
            ->from('AppBundle:Part', 'p');

        $totalItems = $qb->getQuery()->getSingleScalarResult();

        Utilities::setupSearchableEntityQueryBuild($qb, $request);

        $totalCount = $qb->getQuery()->getSingleScalarResult();

        $qb->select('p')
            ->orderBy('p.id', 'DESC')
            ->setMaxResults($perPage)
            ->setFirstResult($page*$perPage);

        $items = $qb->getQuery()->getResult();

        return ['total_count'=> (int)$totalCount, 'total_items' => (int)$totalItems, 'list'=>$items];
    }

    /**
     * @Rest\Get("/part/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getPartAction(\AppBundle\Entity\Part $part)
    {
        return $part;
    }

    /**
     * @Rest\Post("/part")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("part", converter="fos_rest.request_body")
     */
    public function createPartAction(\AppBundle\Entity\Part $part)
    {
    	$em = $this->getDoctrine()->getManager();
	    $em->persist($part);
	    $em->flush();
        return $part;
    }

    /**
     * @Rest\Put("/part/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("part", converter="fos_rest.request_body")
     */
    public function updatePartAction(\AppBundle\Entity\Part $part)
    {
        $em = $this->getDoctrine()->getManager();
        $em->merge($part);
        $em->flush();
        return $part;
    }

    /**
     * @Rest\Delete("/part/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function deletePartAction(\AppBundle\Entity\Part $part)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($part);
        $em->flush();
        return $part;
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
            ->from('AppBundle:PartCategory', 'pc');

        $totalItems = $qb->getQuery()->getSingleScalarResult();

        Utilities::setupSearchableEntityQueryBuild($qb, $request);

        $totalCount = $qb->getQuery()->getSingleScalarResult();

        $qb->select('pc')
            ->orderBy('pc.id', 'DESC')
            ->setMaxResults($perPage)
            ->setFirstResult($page*$perPage);

        $items = $qb->getQuery()->getResult();

        return ['total_count'=> (int)$totalCount, 'total_items' => (int)$totalItems, 'list'=>$items];
    }

    /**
     * @Rest\Get("/part_category/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getPartCategoryAction(\AppBundle\Entity\PartCategory $partCategory)
    {
        return $partCategory;
    }

    /**
     * @Rest\Post("/part_category")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("partCategory", converter="fos_rest.request_body")
     */
    public function createPartCategoryAction(\AppBundle\Entity\PartCategory $partCategory)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($partCategory);
        $em->flush();
        return $partCategory;
    }

    /**
     * @Rest\Put("/part_category/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("partCategory", converter="fos_rest.request_body")
     */
    public function updatePartCategoryAction(\AppBundle\Entity\PartCategory $partCategory)
    {
        $em = $this->getDoctrine()->getManager();
        $em->merge($partCategory);
        $em->flush();
        return $partCategory;
    }

    /**
     * @Rest\Delete("/part_category/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function deletePartCategoryAction(\AppBundle\Entity\PartCategory $partCategory)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($partCategory);
        $em->flush();
        return $partCategory;
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
            ->from('AppBundle:PartGroup', 'pg');

        $totalItems = $qb->getQuery()->getSingleScalarResult();

        Utilities::setupSearchableEntityQueryBuild($qb, $request);

        $totalCount = $qb->getQuery()->getSingleScalarResult();

        $qb->select('pg')
            ->orderBy('pg.id', 'DESC')
            ->setMaxResults($perPage)
            ->setFirstResult($page*$perPage);

        $items = $qb->getQuery()->getResult();

        return ['total_count'=> (int)$totalCount, 'total_items' => (int)$totalItems, 'list'=>$items];
    }

    /**
     * @Rest\Get("/part_group/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getPartGroupAction(\AppBundle\Entity\PartGroup $partGroup)
    {
        return $partGroup;
    }

    /**
     * @Rest\Post("/part_group")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("partGroup", converter="fos_rest.request_body")
     */
    public function createPartGroupAction(\AppBundle\Entity\PartGroup $partGroup)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($partGroup);
        $em->flush();
        return $partGroup;
    }

    /**
     * @Rest\Put("/part_group/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("partGroup", converter="fos_rest.request_body")
     */
    public function updatePartGroupAction(\AppBundle\Entity\PartGroup $partGroup)
    {
        $em = $this->getDoctrine()->getManager();
        $em->merge($partGroup);
        $em->flush();
        return $partGroup;
    }

    /**
     * @Rest\Delete("/part_group/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function deletePartGroupAction(\AppBundle\Entity\PartGroup $partGroup)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($partGroup);
        $em->flush();
        return $partGroup;
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
            ->from('AppBundle:BinType', 'bt');

        $totalItems = $qb->getQuery()->getSingleScalarResult();

        Utilities::setupSearchableEntityQueryBuild($qb, $request);

        $totalCount = $qb->getQuery()->getSingleScalarResult();

        $qb->select('bt')
            ->orderBy('bt.id', 'DESC')
            ->setMaxResults($perPage)
            ->setFirstResult($page*$perPage);

        $items = $qb->getQuery()->getResult();

        return ['total_count'=> (int)$totalCount, 'total_items' => (int)$totalItems, 'list'=>$items];
    }

    /**
     * @Rest\Get("/bin_type/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getBinTypeAction(\AppBundle\Entity\BinType $binType)
    {
        return $binType;
    }

    /**
     * @Rest\Post("/bin_type")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("binType", converter="fos_rest.request_body")
     */
    public function createBinTypeAction(\AppBundle\Entity\BinType $binType)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($binType);
        $em->flush();
        return $binType;
    }

    /**
     * @Rest\Put("/bin_type/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("binType", converter="fos_rest.request_body")
     */
    public function updateBinTypeAction(\AppBundle\Entity\BinType $binType)
    {
        $em = $this->getDoctrine()->getManager();
        $em->merge($binType);
        $em->flush();
        return $binType;
    }

    /**
     * @Rest\Delete("/bin_type/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function deleteBinTypeAction(\AppBundle\Entity\BinType $binType)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($binType);
        $em->flush();
        return $binType;
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
            ->from('AppBundle:Bin', 'b');

        $totalItems = $qb->getQuery()->getSingleScalarResult();

        Utilities::setupSearchableEntityQueryBuild($qb, $request);

        $totalCount = $qb->getQuery()->getSingleScalarResult();

        $qb->select('b')
            ->orderBy('b.id', 'DESC')
            ->setMaxResults($perPage)
            ->setFirstResult($page*$perPage);

        $items = $qb->getQuery()->getResult();

        return ['total_count'=> (int)$totalCount, 'total_items' => (int)$totalItems, 'list'=>$items];
    }

    /**
     * @Rest\Get("/bin/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getBinAction(\AppBundle\Entity\Bin $bin)
    {
        return $bin;
    }

    /**
     * @Rest\Post("/bin")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("bin", converter="fos_rest.request_body")
     */
    public function createBinAction(\AppBundle\Entity\Bin $bin)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($bin);
        $em->flush();
        return $bin;
    }

    /**
     * @Rest\Put("/bin/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("bin", converter="fos_rest.request_body")
     */
    public function updateBinAction(\AppBundle\Entity\Bin $bin)
    {
        $em = $this->getDoctrine()->getManager();
        $em->merge($bin);
        $em->flush();
        return $bin;
    }

    /**
     * @Rest\Delete("/bin/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function deleteBinAction(\AppBundle\Entity\Bin $bin)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($bin);
        $em->flush();
        return $bin;
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

        return ['total_count'=> (int)$totalCount, 'total_items' => (int)$totalItems, 'list'=>$items];
    }

    /**
     * @Rest\Get("/inventory_movement_rule/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getInventoryMovementRuleAction(\AppBundle\Entity\InventoryMovementRule $inventoryMovementRule)
    {
        return $inventoryMovementRule;
    }

    /**
     * @Rest\Post("/inventory_movement_rule")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("inventoryMovementRule", converter="fos_rest.request_body")
     */
    public function createInventoryMovementRuleAction(\AppBundle\Entity\InventoryMovementRule $inventoryMovementRule)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($inventoryMovementRule);
        $em->flush();
        return $inventoryMovementRule;
    }

    /**
     * @Rest\Put("/inventory_movement_rule/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("inventoryMovementRule", converter="fos_rest.request_body")
     */
    public function updateInventoryMovementRuleAction(\AppBundle\Entity\InventoryMovementRule $inventoryMovementRule)
    {
        $em = $this->getDoctrine()->getManager();
        $em->merge($inventoryMovementRule);
        $em->flush();
        return $inventoryMovementRule;
    }

    /**
     * @Rest\Delete("/inventory_movement_rule/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function deleteInventoryMovementRuleAction(\AppBundle\Entity\InventoryMovementRule $inventoryMovementRule)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($inventoryMovementRule);
        $em->flush();
        return $inventoryMovementRule;
    }





}