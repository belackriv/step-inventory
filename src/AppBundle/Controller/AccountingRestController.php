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
            ->from('AppBundle:Client', 'e')
            ->where('e.organization = :org')
            ->setParameter('org', $this->getUser()->getOrganization());

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
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getClientAction(\AppBundle\Entity\Client $client)
    {
        if( $this->get('security.authorization_checker')->isGranted('VIEW', $client) and
            $client->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            return $client;
        }else{
            throw $this->createNotFoundException('Client #'.$client->getId().' Not Found');
        }
    }

    /**
     * @Rest\Post("/client")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("client", converter="fos_rest.request_body")
     */
    public function createClientAction(\AppBundle\Entity\Client $client)
    {
        if($this->get('security.authorization_checker')->isGranted('CREATE', $client)){
            $em = $this->getDoctrine()->getManager();
            $client->setOrganization($this->getUser()->getOrganization());
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
            $em->detach($client);
            $liveClient = $this->getDoctrine()->getRepository('AppBundle:Client')->findOneById($client->getId());
            if( $client->isOwnedByOrganization($this->getUser()->getOrganization()) and
                $liveClient->isOwnedByOrganization($this->getUser()->getOrganization())
            ){
                $em->merge($client);
                $em->flush();
                return $client;
            }else{
                throw $this->createNotFoundException('Organization #'.$client->getOrganization()->getId().' Not Found');
            }
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
        if( $this->get('security.authorization_checker')->isGranted('DELETE', $client) and
            $client->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
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
            ->from('AppBundle:Customer', 'e')
            ->where('e.organization = :org')
            ->setParameter('org', $this->getUser()->getOrganization());;;

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
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getCustomerAction(\AppBundle\Entity\Customer $customer)
    {
        if( $this->get('security.authorization_checker')->isGranted('VIEW', $customer) and
            $customer->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            return $customer;
        }else{
            throw $this->createNotFoundException('Customer #'.$customer->getId().' Not Found');
        }
    }

    /**
     * @Rest\Post("/customer")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("customer", converter="fos_rest.request_body")
     */
    public function createCustomerAction(\AppBundle\Entity\Customer $customer)
    {
        if($this->get('security.authorization_checker')->isGranted('CREATE', $customer)){
            $em = $this->getDoctrine()->getManager();
            $customer->setOrganization($this->getUser()->getOrganization());
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
            $em->detach($customer);
            $liveCustomer = $this->getDoctrine()->getRepository('AppBundle:Customer')->findOneById($customer->getId());
            if( $customer->isOwnedByOrganization($this->getUser()->getOrganization()) &&
                $liveCustomer->isOwnedByOrganization($this->getUser()->getOrganization())
            ){
                $em->merge($customer);
                $em->flush();
                return $customer;
            }else{
                throw $this->createNotFoundException('Organization #'.$customer->getOrganization()->getId().' Not Found');
            }
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
        if( $this->get('security.authorization_checker')->isGranted('DELETE', $customer) and
            $customer->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
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
            ->from('AppBundle:InboundOrder', 'e')
            ->join('e.client', 'c')
            ->where('c.organization = :org')
            ->setParameter('org', $this->getUser()->getOrganization());

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
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getInboundOrderAction(\AppBundle\Entity\InboundOrder $inboundOrder)
    {
        if( $this->get('security.authorization_checker')->isGranted('VIEW', $inboundOrder) and
            $inboundOrder->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            return $inboundOrder;
        }else{
            throw $this->createNotFoundException('InboundOrder #'.$inboundOrder->getId().' Not Found');
        }
    }

    /**
     * @Rest\Post("/inbound_order")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("inboundOrder", converter="fos_rest.request_body")
     */
    public function createInboundOrderAction(\AppBundle\Entity\InboundOrder $inboundOrder)
    {
        if( $this->get('security.authorization_checker')->isGranted('CREATE', $inboundOrder) and
            $inboundOrder->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
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
            $em->detach($inboundOrder);
            $liveInboundOrder = $em->getRepository('AppBundle:InboundOrder')->findOneById($inboundOrder->getId());
            if( $inboundOrder->isOwnedByOrganization($this->getUser()->getOrganization()) &&
                $liveInboundOrder->isOwnedByOrganization($this->getUser()->getOrganization())
            ){
                $em->merge($inboundOrder);
                $em->flush();
                return $inboundOrder;
            }else{
                throw $this->createAccessDeniedException();
            }
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
        if( $this->get('security.authorization_checker')->isGranted('DELETE', $inboundOrder) and
            $inboundOrder->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
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
            ->from('AppBundle:OutboundOrder', 'e')
            ->join('e.customer', 'c')
            ->where('c.organization = :org')
            ->setParameter('org', $this->getUser()->getOrganization());

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
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getOutboundOrderAction(\AppBundle\Entity\OutboundOrder $outboundOrder)
    {
        if( $this->get('security.authorization_checker')->isGranted('VIEW', $outboundOrder) and
            $outboundOrder->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            return $outboundOrder;
        }else{
            throw $this->createNotFoundException('OutboundOrder #'.$outboundOrder->getId().' Not Found');
        }
    }

    /**
     * @Rest\Post("/outbound_order")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("outboundOrder", converter="fos_rest.request_body")
     */
    public function createOutboundOrderAction(\AppBundle\Entity\OutboundOrder $outboundOrder)
    {
        if( $this->get('security.authorization_checker')->isGranted('CREATE', $outboundOrder) and
            $outboundOrder->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
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
            $em->detach($outboundOrder);
            $liveOutboundOrder = $em->getRepository('AppBundle:OutboundOrder')->findOneById($outboundOrder->getId());
            if( $outboundOrder->isOwnedByOrganization($this->getUser()->getOrganization()) and
                $liveOutboundOrder->isOwnedByOrganization($this->getUser()->getOrganization())
            ){
                $em->merge($outboundOrder);
                $em->flush();
                return $outboundOrder;
            }else{
               throw $this->createAccessDeniedException();
            }
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
        if( $this->get('security.authorization_checker')->isGranted('DELETE', $outboundOrder) and
            $outboundOrder->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            $em = $this->getDoctrine()->getManager();
            $em->remove($outboundOrder);
            $em->flush();
            return $outboundOrder;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

     /**
     * @Rest\Get("/contact")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default","Contact"})
     */
    public function listContactAction()
    {
        $items = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('c')
            ->from('AppBundle:Contact', 'c')
            ->leftJoin('c.client', 'cl')
            ->leftJoin('c.customer', 'cu')
            ->where('cl.organization = :org')
            ->orWhere('cu.organization = :org')
            ->setParameter('org', $this->getUser()->getOrganization())
            ->getQuery()->getResult();;

        $itemlist = array();
        $authorizationChecker = $this->get('security.authorization_checker');
        foreach($items as $item){
            if (true === $authorizationChecker->isGranted('VIEW', $item)) {
                $itemlist[] = $item;
            }
        }

        return array('list'=>$itemlist);
    }

    /**
     * @Rest\Get("/contact/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default","Contact"})
     */
    public function getContactAction(\AppBundle\Entity\Contact $contact)
    {
        if( $this->get('security.authorization_checker')->isGranted('VIEW', $contact) and
            $contact->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            return $contact;
        }else{
            throw $this->createNotFoundException('Contact #'.$contact->getId().' Not Found');
        }
    }

    /**
     * @Rest\Post("/contact")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default","Contact"})
     * @ParamConverter("contact", converter="fos_rest.request_body")
     */
    public function createContactAction(\AppBundle\Entity\Contact $contact)
    {
        if( $this->get('security.authorization_checker')->isGranted('CREATE', $contact) and
            $contact->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            $em = $this->getDoctrine()->getManager();
            $em->persist($contact);
            $em->flush();
            $this->updateAclByRoles($contact, ['ROLE_USER'=>'view', 'ROLE_ADMIN'=>'operator']);
            return $contact;
        }else{
             throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Put("/contact/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default","Contact"})
     * @ParamConverter("contact", converter="fos_rest.request_body")
     */
    public function updateContactAction(\AppBundle\Entity\Contact $contact)
    {
        if($this->get('security.authorization_checker')->isGranted('EDIT', $contact)){
            $em = $this->getDoctrine()->getManager();
            $em->detach($contact);
            $liveContact = $this->getDoctrine()->getRepository('AppBundle:Contact')->findOneById($contact->getId());
            if( $contact->isOwnedByOrganization($this->getUser()->getOrganization()) and
                $liveContact->isOwnedByOrganization($this->getUser()->getOrganization())
            ){
                $em->merge($contact);
                $em->flush();
                return $contact;
            }else{
                throw $this->createNotFoundException('Contact #'.$contact->getId().' Not Found');
            }
        }else{
             throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Delete("/contact/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default","Contact"})
     */
    public function deleteContactAction(\AppBundle\Entity\Contact $contact)
    {
        if( $this->get('security.authorization_checker')->isGranted('DELETE', $contact) and
            $contact->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            $em = $this->getDoctrine()->getManager();
            $em->remove($contact);
            $em->flush();
            return $contact;
        }else{
             throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Get("/address")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default","Address"})
     */
    public function listAddressAction()
    {
        $items = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('a')
            ->from('AppBundle:Address', 'a')
            ->leftJoin('a.client', 'cl')
            ->leftJoin('a.customer', 'cu')
            ->where('cl.organization = :org')
            ->orWhere('cu.organization = :org')
            ->setParameter('org', $this->getUser()->getOrganization())
            ->getQuery()->getResult();;

        $itemlist = array();
        $authorizationChecker = $this->get('security.authorization_checker');
        foreach($items as $item){
            if (true === $authorizationChecker->isGranted('VIEW', $item)) {
                $itemlist[] = $item;
            }
        }

        return array('list'=>$itemlist);
    }

    /**
     * @Rest\Get("/address/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default","Address"})
     */
    public function getAddressAction(\AppBundle\Entity\Address $address)
    {
        if( $this->get('security.authorization_checker')->isGranted('VIEW', $address) and
            $address->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            return $address;
        }else{
            throw $this->createNotFoundException('Address #'.$address->getId().' Not Found');
        }
    }

    /**
     * @Rest\Post("/address")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default","Address"})
     * @ParamConverter("address", converter="fos_rest.request_body")
     */
    public function createAddressAction(\AppBundle\Entity\Address $address)
    {
        if( $this->get('security.authorization_checker')->isGranted('CREATE', $address) and
            $address->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            $em = $this->getDoctrine()->getManager();
            $em->persist($address);
            $em->flush();
            $this->updateAclByRoles($address, ['ROLE_USER'=>'view', 'ROLE_ADMIN'=>'operator']);
            return $address;
        }else{
             throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Put("/address/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default","Address"})
     * @ParamConverter("address", converter="fos_rest.request_body")
     */
    public function updateAddressAction(\AppBundle\Entity\Address $address)
    {
        if($this->get('security.authorization_checker')->isGranted('EDIT', $address)){
            $em = $this->getDoctrine()->getManager();
            $em->detach($address);
            $liveAddress = $this->getDoctrine()->getRepository('AppBundle:Address')->findOneById($address->getId());
            if( $address->isOwnedByOrganization($this->getUser()->getOrganization()) and
                $liveAddress->isOwnedByOrganization($this->getUser()->getOrganization())
            ){
                $em->merge($address);
                $em->flush();
                return $address;
            }else{
                throw $this->createNotFoundException('Address #'.$address->getId().' Not Found');
            }
        }else{
             throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Delete("/address/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default","Address"})
     */
    public function deleteAddressAction(\AppBundle\Entity\Address $address)
    {
        if( $this->get('security.authorization_checker')->isGranted('DELETE', $address) and
            $address->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            $em = $this->getDoctrine()->getManager();
            $em->remove($address);
            $em->flush();
            return $address;
        }else{
             throw $this->createAccessDeniedException();
        }
    }

}