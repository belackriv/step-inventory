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


class AccountingRestController extends FOSRestController
{

    use Mixin\RestPatchMixin;
    use Mixin\UpdateAclMixin;
    use Mixin\WampUpdatePusher;

    /**
     * @Rest\Get("/client")
     * @Rest\Get("/admin_accounting")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function listClientAction(Request $request)
    {
        $page = (int)$request->query->get('page') - 1;
        $perPage =(int)$request->query->get('per_page');
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('COUNT(e.id)')
            ->from('AppBundle:Client', 'e');

        $totalItems = $qb->getQuery()->getSingleScalarResult();

        Utilities::setupSearchableEntityQueryBuild($qb, $request);

        $totalCount = $qb->getQuery()->getSingleScalarResult();

        $qb->select('e')
            ->orderBy('e.id', 'DESC')
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
     * @Rest\Get("/client/{id}")
     * @Rest\View(template=":default:get_travelerid.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getClientAction(\AppBundle\Entity\Client $client)
    {
        if($this->get('security.authorization_checker')->isGranted('VIEW', $client)){
            return $client;
        }else{
            throw $this->createNotFoundException('Client #'.$client->getId().' Not Found');
        }
    }

    /**
     * @Rest\Post("/client")
     * @Rest\View(template=":default:create_travelerid.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("client", converter="fos_rest.request_body")
     */
    public function createClientAction(\AppBundle\Entity\Client $client)
    {
        if($this->get('security.authorization_checker')->isGranted('CREATE', $client)){
            $em = $this->getDoctrine()->getManager();
            $em->persist($client);
            $em->flush();
            $this->updateAclByRoles($client, ['ROLE_USER'=>'view', 'ROLE_ADMIN'=>'operator']);
            return $client;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Put("/client/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("client", converter="fos_rest.request_body")
     */
    public function updateClientAction(\AppBundle\Entity\Client $client)
    {
        if($this->get('security.authorization_checker')->isGranted('EDIT', $client)){
            $em = $this->getDoctrine()->getManager();
            $em->merge($client);
            $em->flush();
            return $client;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Patch("/client/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("client", converter="fos_rest.request_body")
     */
    public function patchClientAction(\AppBundle\Entity\Client $client, $id)
    {
        if($this->get('security.authorization_checker')->isGranted('EDIT', $client)){
            $em = $this->getDoctrine()->getManager();
            $liveClient = $em->getRepository('AppBundle:Client')->findOneById($id);
            $this->patchEntity($liveClient, $client);
            $em->flush();
            return $client;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Delete("/client/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function deleteClientAction(\AppBundle\Entity\Client $client)
    {
        if($this->get('security.authorization_checker')->isGranted('DELETE', $client)){
            $em = $this->getDoctrine()->getManager();
            $em->remove($client);
            $em->flush();
            return $client;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Get("/customer")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function listCustomerAction(Request $request)
    {
        $page = (int)$request->query->get('page') - 1;
        $perPage =(int)$request->query->get('per_page');
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('COUNT(e.id)')
            ->from('AppBundle:Customer', 'e');

        $totalItems = $qb->getQuery()->getSingleScalarResult();

        Utilities::setupSearchableEntityQueryBuild($qb, $request);

        $totalCount = $qb->getQuery()->getSingleScalarResult();

        $qb->select('e')
            ->orderBy('e.id', 'DESC')
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
     * @Rest\Get("/customer/{id}")
     * @Rest\View(template=":default:get_travelerid.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getCustomerAction(\AppBundle\Entity\Customer $customer)
    {
        if($this->get('security.authorization_checker')->isGranted('VIEW', $customer)){
            return $customer;
        }else{
            throw $this->createNotFoundException('Customer #'.$customer->getId().' Not Found');
        }
    }

    /**
     * @Rest\Post("/customer")
     * @Rest\View(template=":default:create_travelerid.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("customer", converter="fos_rest.request_body")
     */
    public function createCustomerAction(\AppBundle\Entity\Customer $customer)
    {
        if($this->get('security.authorization_checker')->isGranted('CREATE', $customer)){
            $em = $this->getDoctrine()->getManager();
            $em->persist($customer);
            $em->flush();
            $this->updateAclByRoles($customer, ['ROLE_USER'=>'view', 'ROLE_ADMIN'=>'operator']);
            return $customer;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Put("/customer/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("customer", converter="fos_rest.request_body")
     */
    public function updateCustomerAction(\AppBundle\Entity\Customer $customer)
    {
        if($this->get('security.authorization_checker')->isGranted('EDIT', $customer)){
            $em = $this->getDoctrine()->getManager();
            $em->merge($customer);
            $em->flush();
            return $customer;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

     /**
     * @Rest\Patch("/customer/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("customer", converter="fos_rest.request_body")
     */
    public function patchCustomerAction(\AppBundle\Entity\Customer $customer, $id)
    {
        if($this->get('security.authorization_checker')->isGranted('EDIT', $customer)){
            $em = $this->getDoctrine()->getManager();
            $liveCustomer = $em->getRepository('AppBundle:Customer')->findOneById($id);
            $this->patchEntity($liveCustomer, $customer);
            $em->flush();
            return $customer;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Delete("/customer/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function deleteCustomerAction(\AppBundle\Entity\Client $customer)
    {
        if($this->get('security.authorization_checker')->isGranted('DELETE', $customer)){
            $em = $this->getDoctrine()->getManager();
            $em->remove($customer);
            $em->flush();
            return $customer;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Get("/inbound_order")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function listInboundOrderAction(Request $request)
    {
        $page = (int)$request->query->get('page') - 1;
        $perPage =(int)$request->query->get('per_page');
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('COUNT(e.id)')
            ->from('AppBundle:InboundOrder', 'e');

        $totalItems = $qb->getQuery()->getSingleScalarResult();

        Utilities::setupSearchableEntityQueryBuild($qb, $request);

        $totalCount = $qb->getQuery()->getSingleScalarResult();

        $qb->select('e')
            ->orderBy('e.id', 'DESC')
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
     * @Rest\Get("/inbound_order/{id}")
     * @Rest\View(template=":default:get_travelerid.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getInboundOrderAction(\AppBundle\Entity\InboundOrder $inboundOrder)
    {
        if($this->get('security.authorization_checker')->isGranted('VIEW', $inboundOrder)){
            return $inboundOrder;
        }else{
            throw $this->createNotFoundException('InboundOrder #'.$inboundOrder->getId().' Not Found');
        }
    }

    /**
     * @Rest\Post("/inbound_order")
     * @Rest\View(template=":default:create_travelerid.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("inboundOrder", converter="fos_rest.request_body")
     */
    public function createInboundOrderAction(\AppBundle\Entity\InboundOrder $inboundOrder)
    {
        if($this->get('security.authorization_checker')->isGranted('CREATE', $inboundOrder)){
            $em = $this->getDoctrine()->getManager();
            $em->persist($inboundOrder);
            $inboundOrder->setLabel('');
            $em->flush();
            $inboundOrder->generateLabel();
            $em->flush();
            $this->updateAclByRoles($inboundOrder, ['ROLE_USER'=>'view', 'ROLE_ADMIN'=>'operator']);
            return $inboundOrder;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Put("/inbound_order/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("inboundOrder", converter="fos_rest.request_body")
     */
    public function updateInboundOrderAction(\AppBundle\Entity\InboundOrder $inboundOrder)
    {
        if($this->get('security.authorization_checker')->isGranted('EDIT', $inboundOrder)){
            $em = $this->getDoctrine()->getManager();
            $em->merge($inboundOrder);
            $em->flush();
            return $inboundOrder;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

     /**
     * @Rest\Patch("/inbound_order/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("inboundOrder", converter="fos_rest.request_body")
     */
    public function patchInboundOrderAction(\AppBundle\Entity\InboundOrder $inboundOrder, $id)
    {
        if($this->get('security.authorization_checker')->isGranted('EDIT', $inboundOrder)){
            $em = $this->getDoctrine()->getManager();
            $liveInboundOrder = $em->getRepository('AppBundle:InboundOrder')->findOneById($id);
            $this->patchEntity($liveInboundOrder, $inboundOrder);
            $em->flush();
            return $inboundOrder;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Delete("/inbound_order/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function deleteInboundOrderAction(\AppBundle\Entity\InboundOrder $inboundOrder)
    {
        if($this->get('security.authorization_checker')->isGranted('DELETE', $inboundOrder)){
            $em = $this->getDoctrine()->getManager();
            $em->remove($inboundOrder);
            $em->flush();
            return $inboundOrder;
        }else{
            throw $this->createAccessDeniedException();
        }
    }


    /**
     * @Rest\Get("/outbound_order")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function listOutboundOrderAction(Request $request)
    {
        $page = (int)$request->query->get('page') - 1;
        $perPage =(int)$request->query->get('per_page');
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('COUNT(e.id)')
            ->from('AppBundle:OutboundOrder', 'e');

        $totalItems = $qb->getQuery()->getSingleScalarResult();

        Utilities::setupSearchableEntityQueryBuild($qb, $request);

        $totalCount = $qb->getQuery()->getSingleScalarResult();

        $qb->select('e')
            ->orderBy('e.id', 'DESC')
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
     * @Rest\Get("/outbound_order/{id}")
     * @Rest\View(template=":default:get_travelerid.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getOutboundOrderAction(\AppBundle\Entity\OutboundOrder $outboundOrder)
    {
        if($this->get('security.authorization_checker')->isGranted('VIEW', $outboundOrder)){
            return $outboundOrder;
        }else{
            throw $this->createNotFoundException('OutboundOrder #'.$outboundOrder->getId().' Not Found');
        }
    }

    /**
     * @Rest\Post("/outbound_order")
     * @Rest\View(template=":default:create_travelerid.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("outboundOrder", converter="fos_rest.request_body")
     */
    public function createOutboundOrderAction(\AppBundle\Entity\OutboundOrder $outboundOrder)
    {
        if($this->get('security.authorization_checker')->isGranted('CREATE', $outboundOrder)){
            $em = $this->getDoctrine()->getManager();
            $em->persist($outboundOrder);
            $outboundOrder->setLabel('');
            $em->flush();
            $outboundOrder->generateLabel();
            $em->flush();
            $this->updateAclByRoles($outboundOrder, ['ROLE_USER'=>'view', 'ROLE_ADMIN'=>'operator']);
            return $outboundOrder;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Put("/outbound_order/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("outboundOrder", converter="fos_rest.request_body")
     */
    public function updateOutboundOrderAction(\AppBundle\Entity\OutboundOrder $outboundOrder)
    {
        if($this->get('security.authorization_checker')->isGranted('EDIT', $outboundOrder)){
            $em = $this->getDoctrine()->getManager();
            $em->merge($outboundOrder);
            $em->flush();
            return $outboundOrder;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

     /**
     * @Rest\Patch("/outbound_order/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("outboundOrder", converter="fos_rest.request_body")
     */
    public function patchOutboundOrderAction(\AppBundle\Entity\OutboundOrder $outboundOrder, $id)
    {
        if($this->get('security.authorization_checker')->isGranted('EDIT', $outboundOrder)){
            $em = $this->getDoctrine()->getManager();
            $liveOutboundOrder = $em->getRepository('AppBundle:OutboundOrder')->findOneById($id);
            $this->patchEntity($liveOutboundOrder, $outboundOrder);
            $em->flush();
            return $outboundOrder;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Delete("/outbound_order/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function deleteOutboundOrderAction(\AppBundle\Entity\OutboundOrder $outboundOrder)
    {
        if($this->get('security.authorization_checker')->isGranted('DELETE', $outboundOrder)){
            $em = $this->getDoctrine()->getManager();
            $em->remove($outboundOrder);
            $em->flush();
            return $outboundOrder;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

}