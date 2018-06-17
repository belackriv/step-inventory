<?php

namespace AppBundle\Controller;

use AppBundle\Library\Utilities;
use AppBundle\Library\Service\UploadException;
use AppBundle\Library\Service\SncRedisSessionQueryService;
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


class DefaultRestController extends FOSRestController
{
    use Mixin\RestPatchMixin;
    use Mixin\UpdateAclMixin;
    use Mixin\WampUpdatePusher;

    /**
     * @Rest\Get("/account")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function listAccountAction(Request $request)
    {
        $account = $this->getUser()->getOrganization()->getAccount();
        $account->stripePublicKey = $this->container->getParameter('stripe_public_key');

        $redisClient = $this->container->get('snc_redis.default');
        $sessionQueryService = new SncRedisSessionQueryService($redisClient);
        $account->currentSessions = $sessionQueryService->getSessions(
            $this->container->get('snc_redis.session.handler'),
            $this->container->get('session.storage.native'),
            $this->getUser()->getOrganization()
        );

        if($account->getSubscription()){
            $organization = $this->getUser()->getOrganization();
            $account->monthlyTravelerIds = $this->container->get('app.tid_init')->getTravelerIdsInRange(
                $organization,
                $organization->getAccount()->getSubscription()->getCurrentPeriodStart(),
                $organization->getAccount()->getSubscription()->getCurrentPeriodEnd()
            );
        }
        return ['total_count'=> 1, 'total_items' => 1, 'list'=>[$account]];
    }

    /**
     * @Rest\Delete("/current_session/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function deleteCurrentSessionAction($id, Request $request)
    {
        $redisClient = $this->container->get('snc_redis.default');
        $sessionQueryService = new SncRedisSessionQueryService($redisClient);
        $sessionQueryService->destroySession(
            $id,
            $this->getUser()->getOrganization()
        );
        return [];
    }

    /**
     * @Rest\Get("/profile")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getProfileAction(Request $request)
    {
        return $this->getUser();
    }

    /**
     * @Rest\Put("/profile")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("profileData", class="stdClass", converter="fos_rest.request_body")
     */
    public function updateProfileAction($profileData, Request $request)
    {
        $myself = $this->getUser();
        $profile = (object)$profileData;
        $myself->setUsername($profile->username);
        $myself->setFirstName($profile->firstName);
        $myself->setLastName($profile->lastName);
        $myself->setEmail($profile->email);
        $em = $this->getDoctrine()->getManager()->flush();
        return $profile;
    }

    /**
     * @Rest\Post("/account_change")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("accountChange",  converter="fos_rest.request_body")
     */
    public function createAccountChangeAction(\AppBundle\Entity\AccountChange $accountChange, Request $request)
    {
        \Stripe\Stripe::setApiKey($this->container->getParameter('stripe_secure_key'));
        $accountChange->setAccount($this->getUser()->getOrganization()->getAccount());
        $accountChange->setChangedBy($this->getUser());
        $accountChange->setChangedAt(new \DateTime);
        $this->getDoctrine()->getManager()->persist($accountChange);
        $accountChange->updateAccount();
        $this->getDoctrine()->getManager()->flush();
        return $accountChange;
    }

    /**
     * @Rest\Post("/payment_source")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("paymentSource",  converter="fos_rest.request_body")
     */
    public function createPaymentSourceAction(\AppBundle\Entity\PaymentSource $paymentSource, Request $request)
    {
        $account = $this->getUser()->getOrganization()->getAccount();
        \Stripe\Stripe::setApiKey($this->container->getParameter('stripe_secure_key'));
        $stripeCustomer = \Stripe\Customer::retrieve($account->getExternalId());
        $stripePaymentSource = $stripeCustomer->sources->create(["source" => $paymentSource->getExternalId()]);
        $paymentSource = \AppBundle\Entity\PaymentSource::getInstance($stripePaymentSource);
        $paymentSource->updateFromStripe($stripePaymentSource);
        $paymentSource->setAccount($account);
        $this->getDoctrine()->getManager()->persist($paymentSource);
        $this->getDoctrine()->getManager()->flush();
        return $paymentSource;
    }

    /**
     * @Rest\Delete("/payment_source/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function removePaymentSourceAction(\AppBundle\Entity\PaymentSource $paymentSource, Request $request)
    {
        $account = $this->getUser()->getOrganization()->getAccount();
        if($account != $paymentSource->getAccount()){
            throw $this->createAccessDeniedException();
        }
        \Stripe\Stripe::setApiKey($this->container->getParameter('stripe_secure_key'));
        $stripeCustomer = \Stripe\Customer::retrieve($account->getExternalId());
        $stripeCustomer->sources->retrieve($paymentSource->getExternalId())->delete();
        $account->removePaymentSource($paymentSource);
        $paymentSource->setAccount(null);
        $this->getDoctrine()->getManager()->remove($paymentSource);
        $this->getDoctrine()->getManager()->flush();
        return $paymentSource;
    }

     /**
     * @Rest\Post("/subscription")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("subscription",  converter="fos_rest.request_body")
     */
    public function createSubscriptionAction(\AppBundle\Entity\Subscription $subscription, Request $request)
    {
        $account = $this->getUser()->getOrganization()->getAccount();

        if($account->getSubscription() !== null){
            throw new HttpException(500, 'Account Already Has A Subscription');
        }

        \Stripe\Stripe::setApiKey($this->container->getParameter('stripe_secure_key'));
        $stripeSubscription = \Stripe\Subscription::create([
            'customer' => $account->getExternalId(),
            'plan' => $subscription->getPlan()->getExternalId(),
            'trial_end' => 'now'
        ]);
        $subscription->setAccount($account);
        $subscription->updateFromStripe($stripeSubscription);
        $account->setSubscription($subscription);
        $this->getDoctrine()->getManager()->persist($subscription);
        $this->getDoctrine()->getManager()->flush();
        return $subscription;
    }

    /**
     * @Rest\Get("/subscription_cancel")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function cancelSubscriptionAction(Request $request)
    {
        $subscription = $this->getUser()->getOrganization()->getAccount()->getSubscription();
        \Stripe\Stripe::setApiKey($this->container->getParameter('stripe_secure_key'));
        $stripeSubscription = \Stripe\Subscription::retrieve($subscription->getExternalId());
        $stripeSubscription->cancel();
        $subscription->updateFromStripe($stripeSubscription);


        $this->getDoctrine()->getManager()->flush();
        return $subscription;
    }

     /**
     * @Rest\Get("/subscription")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function listSubscritionsAction(Request $request)
    {
        $account = $this->getDoctrine()->getRepository('AppBundle:Account')->findOneBy([
            'organization' => $this->getUser()->getOrganization()
        ]);
        $subscriptions = $this->getDoctrine()->getRepository('AppBundle:Subscription')->findBy([
            'account' => $account
        ]);

        return ['total_count'=> count($subscriptions), 'total_items' => count($subscriptions), 'list'=>$subscriptions];
    }

    /**
     * @Rest\Get("/plan")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function listPlansAction(Request $request)
    {
        $plans = $this->getDoctrine()->getRepository('AppBundle:Plan')->findBy([
            'isActive' => true
        ],['amount' => 'ASC']);

        return ['total_count'=> count($plans), 'total_items' => count($plans), 'list'=>$plans];
    }

    /**
     * @Rest\Get("/organization")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function listOrganizationAction(Request $request)
    {
        $page = (int)$request->query->get('page') - 1;
        $perPage =(int)$request->query->get('per_page');
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('COUNT(o.id)')
            ->from('AppBundle:Organization', 'o');

        if(!$this->get('security.authorization_checker')->isGranted('ROLE_DEV')){
            $qb->where('o.id = :orgId')
            ->setParameter('orgId', $this->getUser()->getOrganization()->getId());
        }


        $totalItems = $qb->getQuery()->getSingleScalarResult();

        Utilities::setupSearchableEntityQueryBuild($qb, $request);

        $totalCount = $qb->getQuery()->getSingleScalarResult();

        $qb->select('o')
            ->orderBy('o.name', 'DESC')
            ->setMaxResults($perPage)
            ->setFirstResult($page*$perPage);

        $items = $qb->getQuery()->getResult();

        $itemlist = array();
        $authorizationChecker = $this->get('security.authorization_checker');
        foreach($items as $item){
            if (true === $authorizationChecker->isGranted('VIEW', $item)) {
                $itemlist[] = $item;
            }
        }

        return ['total_count'=> (int)$totalCount, 'total_items' => (int)$totalItems, 'list'=>$itemlist];
    }

    /**
     * @Rest\Get("/organization/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getOrganizationAction(\AppBundle\Entity\Organization $organization)
    {
        if($this->get('security.authorization_checker')->isGranted('VIEW', $organization)){
            if(!$this->get('security.authorization_checker')->isGranted('ROLE_DEV')){
                if($this->getUser()->getOrganization() === $organization){
                    return $organization;
                }else{
                    throw $this->createNotFoundException('Organization #'.$organization->getId().' Not Found');
                }
            }else{
                return $organization;
            }

        }else{
            throw $this->createNotFoundException('Organization #'.$organization->getId().' Not Found');
        }
    }

    /**
     * @Rest\Post("/organization")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("organization", converter="fos_rest.request_body")
     */
    public function createOrganizationAction(\AppBundle\Entity\Organization $organization)
    {
        if($this->get('security.authorization_checker')->isGranted('ROLE_DEV')){
            $em = $this->getDoctrine()->getManager();
            $em->persist($organization);
            $em->flush();
            $this->updateAclByRoles($organization, ['ROLE_USER'=>'view', 'ROLE_ADMIN'=>'operator']);
            return $organization;
        }else{
             throw $this->createAccessDeniedException();
        }
    }


    /**
     * @Rest\Put("/organization/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("organization", converter="fos_rest.request_body")
     */
    public function updateOrganizationAction(\AppBundle\Entity\Organization $organization)
    {
        if($this->get('security.authorization_checker')->isGranted('EDIT', $organization)){
            $em = $this->getDoctrine()->getManager();
            if( $organization === $this->getUser()->getOrganization() or
                $this->get('security.authorization_checker')->isGranted('ROLE_DEV')
            ){
                $em->merge($organization);
                $em->flush();
                return $organization;
            }else{
                throw $this->createNotFoundException('Organization #'.$organization->getId().' Not Found');
            }
        }else{
             throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Delete("/organization/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function deleteOrganizationAction(\AppBundle\Entity\Organization $organization)
    {
        if( $this->get('security.authorization_checker')->isGranted('DELETE', $organization) and
            (   $organization === $this->getUser()->getOrganization() or
                $this->get('security.authorization_checker')->isGranted('ROLE_DEV') )
        ){
            $em = $this->getDoctrine()->getManager();
            $em->remove($organization);
            $em->flush();
            return $organization;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Get("/office")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default", "ListOffices"})
     */
    public function listOfficeAction()
    {
        $offices = $this->getDoctrine()->getRepository('AppBundle:Office')
            ->findBy(['organization' => $this->getUser()->getOrganization()]);

        $authorizationChecker = $this->get('security.authorization_checker');
        foreach($offices as $office){
            if($this->get('security.authorization_checker')->isGranted('VIEW', $office)){
                foreach($office->getDepartments() as $dept){
                    if($this->get('security.authorization_checker')->isGranted('VIEW', $dept)){
                        foreach($dept->getMenuItems() as $item){
                            if($item->getParent() !== null){
                                $dept->removeMenuItem($item);
                            }
                            $granted = $authorizationChecker->isGranted('VIEW', $item->getMenuLink());
                            if (false === $granted) {
                                $dept->removeMenuItem($item);
                            }
                            foreach($item->getChildren() as $child){
                                $granted = $authorizationChecker->isGranted('VIEW', $child->getMenuLink());
                                if (false === $granted) {
                                    $item->removeChild($child);
                                }
                            }
                        }
                    }
                }
            }
        }

        return ['total_count'=> count($offices), 'total_items' => count($offices), 'list'=>$offices];
    }

    /**
     * @Rest\Get("/office/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getOfficeAction(\AppBundle\Entity\Office $office)
    {
        if( $this->get('security.authorization_checker')->isGranted('VIEW', $office) and
            $office->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            return $office;
        }else{
            throw $this->createNotFoundException('Office #'.$office->getId().' Not Found');
        }
    }

    /**
     * @Rest\Post("/office")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("office", converter="fos_rest.request_body")
     */
    public function createOfficeAction(\AppBundle\Entity\Office $office)
    {
        if($this->get('security.authorization_checker')->isGranted('CREATE', $office)){
            $em = $this->getDoctrine()->getManager();
            $office->setOrganization($this->getUser()->getOrganization());
            $em->persist($office);
            $em->flush();
            $this->updateAclByRoles($office, ['ROLE_USER'=>'view', 'ROLE_ADMIN'=>'operator']);
            return $office;
        }else{
             throw $this->createAccessDeniedException();
        }
    }


    /**
     * @Rest\Put("/office/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("office", converter="fos_rest.request_body")
     */
    public function updateOfficeAction(\AppBundle\Entity\Office $office)
    {

        if($this->get('security.authorization_checker')->isGranted('EDIT', $office)){
            $em = $this->getDoctrine()->getManager();
            $em->detach($office);
            $liveOffice = $this->getDoctrine()->getRepository('AppBundle:Office')->findOneById($office->getId());
            if( $office->isOwnedByOrganization($this->getUser()->getOrganization()) and
                $liveOffice->isOwnedByOrganization($this->getUser()->getOrganization())
            ){
                $em->merge($office);
                $em->flush();
                return $office;
            }else{
                throw $this->createNotFoundException('Organization #'.$office->getOrganization()->getId().' Not Found');
            }
        }else{
             throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Delete("/office/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function deleteOfficeAction(\AppBundle\Entity\Office $office)
    {
        if( $this->get('security.authorization_checker')->isGranted('DELETE', $office) and
            $office->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            $em = $this->getDoctrine()->getManager();
            $em->remove($office);
            $em->flush();
            return $office;
        }else{
             throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Get("/department")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default","Department"})
     */
    public function listDepartmentAction()
    {
        $items = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('d')
            ->from('AppBundle:Department', 'd')
            ->join('d.office', 'o')
            ->where('o.organization = :org')
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
     * @Rest\Get("/department/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default","Department"})
     */
    public function getDepartmentAction(\AppBundle\Entity\Department $department)
    {
        if( $this->get('security.authorization_checker')->isGranted('VIEW', $department) and
            $department->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            return $department;
        }else{
            throw $this->createNotFoundException('Department #'.$department->getId().' Not Found');
        }
    }

    /**
     * @Rest\Post("/department")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default","Department"})
     * @ParamConverter("department", converter="fos_rest.request_body")
     */
    public function createDepartmentAction(\AppBundle\Entity\Department $department)
    {
        if( $this->get('security.authorization_checker')->isGranted('CREATE', $department) and
            $department->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            $em = $this->getDoctrine()->getManager();
            $em->persist($department);
            $em->flush();
            $this->updateAclByRoles($department, ['ROLE_USER'=>'view', 'ROLE_ADMIN'=>'operator']);
            return $department;
        }else{
             throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Put("/department/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default","Department"})
     * @ParamConverter("department", converter="fos_rest.request_body")
     */
    public function updateDepartmentAction(\AppBundle\Entity\Department $department)
    {
        if($this->get('security.authorization_checker')->isGranted('EDIT', $department)){
            $em = $this->getDoctrine()->getManager();
            $em->detach($department);
            $liveDepartment = $this->getDoctrine()->getRepository('AppBundle:Department')->findOneById($department->getId());
            if( $department->isOwnedByOrganization($this->getUser()->getOrganization()) and
                $liveDepartment->isOwnedByOrganization($this->getUser()->getOrganization())
            ){
                $em->merge($department);
                $em->flush();
                return $department;
            }else{
                throw $this->createNotFoundException('Office #'.$department->getOffice()->getId().' Not Found');
            }
        }else{
             throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Delete("/department/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default","Department"})
     */
    public function deleteDepartmentAction(\AppBundle\Entity\Department $department)
    {
        if( $this->get('security.authorization_checker')->isGranted('DELETE', $department) and
            $department->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            $em = $this->getDoctrine()->getManager();
            $em->remove($department);
            $em->flush();
            return $department;
        }else{
             throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Get("/announcement")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default","Department"})
     */
    public function listAnnouncementAction()
    {
        $items = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('a')
            ->from('AppBundle:Announcement', 'a')
            ->where('a.organization = :org')
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
     * @Rest\Get("/announcement/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default","Announcement"})
     */
    public function getAnnouncementAction(\AppBundle\Entity\Announcement $announcement)
    {
        if( $this->get('security.authorization_checker')->isGranted('VIEW', $announcement) and
            $announcement->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            return $announcement;
        }else{
            throw $this->createNotFoundException('Announcement #'.$announcement->getId().' Not Found');
        }
    }

    /**
     * @Rest\Post("/announcement")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default","Announcement"})
     * @ParamConverter("announcement", converter="fos_rest.request_body")
     */
    public function createAnnouncementAction(\AppBundle\Entity\Announcement $announcement)
    {
        if( $this->get('security.authorization_checker')->isGranted('CREATE', $announcement)){
            $em = $this->getDoctrine()->getManager();
            $announcement->setPostedAt(new \DateTime());
            $announcement->setByUser($this->getUser());
            $announcement->setOrganization($this->getUser()->getOrganization());
            $announcement->setIsActive(true);
            $em->persist($announcement);
            $em->flush();
            $this->updateAclByRoles($announcement, ['ROLE_USER'=>'view', 'ROLE_ADMIN'=>'operator']);
            return $announcement;
        }else{
             throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Put("/announcement/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default","Announcement"})
     * @ParamConverter("announcement", converter="fos_rest.request_body")
     */
    public function updateAnnouncementAction(\AppBundle\Entity\Announcement $announcement)
    {
        if($this->get('security.authorization_checker')->isGranted('EDIT', $announcement)){
            $em = $this->getDoctrine()->getManager();
            $em->detach($announcement);
            $liveAnnouncement = $this->getDoctrine()->getRepository('AppBundle:Announcement')->findOneById($announcement->getId());
            if( $announcement->isOwnedByOrganization($this->getUser()->getOrganization()) and
                $liveAnnouncement->isOwnedByOrganization($this->getUser()->getOrganization())
            ){
                $em->merge($announcement);
                $em->flush();
                return $announcement;
            }else{
                throw $this->createNotFoundException('Office #'.$announcement->getOffice()->getId().' Not Found');
            }
        }else{
             throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Delete("/announcement/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default","Announcement"})
     */
    public function deleteAnnouncementAction(\AppBundle\Entity\Announcement $announcement)
    {
        if( $this->get('security.authorization_checker')->isGranted('DELETE', $announcement) and
            $announcement->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            $em = $this->getDoctrine()->getManager();
            $em->remove($announcement);
            $em->flush();
            return $announcement;
        }else{
             throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Get("/menu_item")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"MenuItem"})
     */
    public function listMenuItemAction(Request $request)
    {
        $page = (int)$request->query->get('page') - 1;
        $perPage =(int)$request->query->get('per_page');
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('COUNT(mi.id)')
            ->from('AppBundle:MenuItem', 'mi')
            ->where('mi.organization = :org')
            ->setParameter('org', $this->getUser()->getOrganization());

        $totalItems = $qb->getQuery()->getSingleScalarResult();

        Utilities::setupSearchableEntityQueryBuild($qb, $request);

        $totalCount = $qb->getQuery()->getSingleScalarResult();

        $qb->select('mi')
            ->orderBy('mi.id', 'DESC')
            ->setMaxResults($perPage)
            ->setFirstResult($page*$perPage);

        $items = $qb->getQuery()->getResult();

        $itemlist = array();
        $authorizationChecker = $this->get('security.authorization_checker');
        foreach($items as $menuItem){
            if (true === $authorizationChecker->isGranted('VIEW', $menuItem->getMenuLink())) {
                $itemlist[] = $menuItem;
            }
        }

        return ['total_count'=> (int)$totalCount, 'total_items' => (int)$totalItems, 'list'=>$itemlist];
    }

    /**
     * @Rest\Get("/menu_item/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"MenuItem"})
     */
    public function getMenuItemAction(\AppBundle\Entity\MenuItem $menuItem)
    {
        if( $this->get('security.authorization_checker')->isGranted('VIEW', $menuItem) and
            $menuItem->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            return $menuItem;
        }else{
            throw $this->createNotFoundException('MenuItem #'.$menuItem->getId().' Not Found');
        }
    }

    /**
     * @Rest\Post("/menu_item")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"MenuItem"})
     * @ParamConverter("menuItem", converter="fos_rest.request_body")
     */
    public function createMenuItemAction(\AppBundle\Entity\MenuItem $menuItem)
    {
    	if($this->get('security.authorization_checker')->isGranted('CREATE', $menuItem)){
            $em = $this->getDoctrine()->getManager();
            $menuItem->setOrganization($this->getUser()->getOrganization());
            $em->persist($menuItem);
            $em->flush();
            $this->updateAclByRoles($menuItem, ['ROLE_USER'=>'view', 'ROLE_ADMIN'=>'operator']);
            return $menuItem;
        }else{
             throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Put("/menu_item/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"MenuItem"})
     * @ParamConverter("menuItem", converter="fos_rest.request_body")
     */
    public function updateMenuItemAction(\AppBundle\Entity\MenuItem $menuItem)
    {
        if($this->get('security.authorization_checker')->isGranted('EDIT', $menuItem)){
            $em = $this->getDoctrine()->getManager();
            $em->detach($menuItem);
            $liveMenuItem = $this->getDoctrine()->getRepository('AppBundle:MenuItem')->findOneById($menuItem->getId());
            if( $menuItem->isOwnedByOrganization($this->getUser()->getOrganization()) and
                $liveMenuItem->isOwnedByOrganization($this->getUser()->getOrganization())
            ){
                $em->merge($menuItem);
                $em->flush();
                return $menuItem;
            }else{
                throw $this->createNotFoundException('Organization #'.$menuItem->getOrganization()->getId().' Not Found');
            }
        }else{
             throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Delete("/menu_item/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"MenuItem"})
     */
    public function deleteMenuItemAction(\AppBundle\Entity\MenuItem $menuItem)
    {
        if( $this->get('security.authorization_checker')->isGranted('DELETE', $menuItem) and
            $menuItem->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            $em = $this->getDoctrine()->getManager();
            $em->remove($menuItem);
            $em->flush();
            return $menuItem;
        }else{
             throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Get("/menu_link")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"MenuLink"})
     */
    public function listMenuLinkAction(Request $request)
    {
        $items = $this->getDoctrine()
        ->getRepository('AppBundle:MenuLink')
        ->findAll();

        $itemlist = array();
        if($this->getUser()->getOrganization()->getAccount()->isActive()){
            $authorizationChecker = $this->get('security.authorization_checker');
            foreach($items as $item){
                if (true === $authorizationChecker->isGranted('VIEW', $item)) {
                    $itemlist[] = $item;
                }
            }
        }
        return ['total_count'=> count($itemlist), 'total_itemlist' => count($itemlist), 'list'=>$itemlist];
    }

    /**
     * @Rest\Get("/menu_link/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"MenuLink"})
     */
    public function getMenuLinkAction(\AppBundle\Entity\MenuLink $menuLink)
    {
        if($this->get('security.authorization_checker')->isGranted('VIEW', $menuLink)){
            return $menuLink;
        }else{
            throw $this->createNotFoundException('MenuLink #'.$menuLink->getId().' Not Found');
        }
    }

     /**
     * @Rest\Post("/menu_link")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"MenuLink"})
     * @ParamConverter("menuLink", converter="fos_rest.request_body")
     */
    public function createMenuLinkAction(\AppBundle\Entity\MenuLink $menuLink)
    {
        if($this->get('security.authorization_checker')->isGranted('CREATE', $menuLink)){
            $em = $this->getDoctrine()->getManager();
            $em->persist($menuLink);
            $em->flush();
            $this->updateAclByRoles($menuLink, ['ROLE_USER'=>'view', 'ROLE_DEV'=>'operator']);
            return $menuLink;
        }else{
             throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Put("/menu_link/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"MenuLink"})
     * @ParamConverter("menuLink", converter="fos_rest.request_body")
     */
    public function updateMenuLinkAction(\AppBundle\Entity\MenuLink $menuLink)
    {
        if($this->get('security.authorization_checker')->isGranted('EDIT', $menuLink)){
            $em = $this->getDoctrine()->getManager();
            $em->merge($menuLink);
            $em->flush();
            return $menuLink;
        }else{
             throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Delete("/menu_link/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"MenuLink"})
     */
    public function deleteMenuLinkAction(\AppBundle\Entity\MenuLink $menuLink)
    {
        if($this->get('security.authorization_checker')->isGranted('DELETE', $menuLink)){
            $em = $this->getDoctrine()->getManager();
            $em->remove($menuLink);
            $em->flush();
            return $menuLink;
        }else{
             throw $this->createAccessDeniedException();
        }
    }


    /**
     * @Rest\Post("/login_check")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default", "GetMyself"})
     */
    public function loginCheckAction(Request $request)
    {
        $session = $request->getSession();
        $myself = $this->getUser();
        if($session->get('currentDepartmentId')){
            $department = $this->getDoctrine() ->getRepository('AppBundle:Department')
                ->find($session->get('currentDepartmentId'));
            $myself->currentDepartment = $department;
        }else{
            $myself->currentDepartment = $myself->getDefaultDepartment();
        }
        $myself->appAnnouncement = $this->getDoctrine() ->getRepository('AppBundle:Announcement')
                ->findLatest($this->getUser()->getOrganization());
        $myself->inventoryAlertLogs = $this->getDoctrine() ->getRepository('AppBundle:InventoryAlert')
                ->findActiveLogs($this->getUser()->getOrganization());

        $myself->roleHierarchy = $this->get('security.role_hierarchy')->fetchRoleHierarchy();
        $myself->menuItems = $this->get('app.static_menu_builder')->build();

        return $myself;
    }

    /**
     * @Rest\Get("/myself")
     * @Rest\Get("/myself/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default", "GetMyself"})
     */
    public function getMyself(Request $request)
    {
        $session = $request->getSession();
        $myself = $this->getUser();
        if($session->get('currentDepartmentId')){
            $department = $this->getDoctrine() ->getRepository('AppBundle:Department')
                ->find($session->get('currentDepartmentId'));
            $myself->currentDepartment = $department;
        }else{
            $myself->currentDepartment = $myself->getDefaultDepartment();
        }
        $myself->appAnnouncement = $this->getDoctrine() ->getRepository('AppBundle:Announcement')
                ->findLatest($this->getUser()->getOrganization());
        $myself->inventoryAlertLogs = $this->getDoctrine() ->getRepository('AppBundle:InventoryAlert')
                ->findActiveLogs($this->getUser()->getOrganization());

        $myself->roleHierarchy = $this->get('security.role_hierarchy')->fetchRoleHierarchy();
        $myself->menuItems = $this->get('app.static_menu_builder')->build();

        return $myself;
    }

    /**
     * @Rest\Put("/myself/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default", "GetMyself"})
     * @ParamConverter("myself", converter="fos_rest.request_body")
     */
    public function updateMyself(\AppBundle\Entity\User $myself, Request $request)
    {
        $session = $request->getSession();
        $session->set('currentDepartmentId', $myself->currentDepartment->getId());
        return $myself;
    }


    /**
     * @Rest\Get("/user")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function listUserAction(Request $request)
    {
        $page = (int)$request->query->get('page') - 1;
        $perPage =(int)$request->query->get('per_page');
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('COUNT(u.id)')
            ->from('AppBundle:User', 'u')
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
                $item->roleHierarchy = $this->get('security.role_hierarchy')->fetchRoleHierarchy();
                $itemlist[] = $item;
            }
        }

        return ['total_count'=> (int)$totalCount, 'total_items' => (int)$totalItems, 'list'=>$itemlist];
    }

    /**
     * @Rest\Get("/user/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getUserAction(\AppBundle\Entity\User $user)
    {
        if( $this->get('security.authorization_checker')->isGranted('VIEW', $user) and
            $user->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            $user->roleHierarchy = $this->get('security.role_hierarchy')->fetchRoleHierarchy();
            return $user;
        }else{
            throw $this->createNotFoundException('User #'.$user->getId().' Not Found');
        }
    }

    /**
     * @Rest\Post("/user")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("user", converter="fos_rest.request_body")
     */
    public function createUserAction(\AppBundle\Entity\User $user)
    {
        if($this->get('security.authorization_checker')->isGranted('CREATE', $user)){
            $em = $this->getDoctrine()->getManager();

            $encoder = $this->container->get('security.password_encoder');
            $encoded = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($encoded);

            if(!$this->get('security.authorization_checker')->isGranted('ROLE_DEV')){
                $user->setOrganization($this->getUser()->getOrganization());
            }

            $em->persist($user);
            foreach($user->getUserRoles() as $userRole){
                if($this->get('security.authorization_checker')->isGranted('VIEW', $userRole->getRole())){
                    $userRole->setUser($user);
                    $em->persist($userRole);
                }else{
                    $user->removeUserRole($userRole);
                    $em->remove($userRole);
                }
            }

            $user->roleHierarchy = $this->get('security.role_hierarchy')->fetchRoleHierarchy();
            $em->flush();
            $this->updateAclByRoles($user, ['ROLE_USER'=>'view', 'ROLE_ADMIN'=>'operator']);
            foreach($user->getUserRoles() as $userRole){
                $this->updateAclByRoles($userRole, ['ROLE_USER'=>'view', 'ROLE_ADMIN'=>'operator']);
            }
            return $user;
        }else{
             throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Put("/user/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("user", converter="fos_rest.request_body")
     */
    public function updateUserAction(\AppBundle\Entity\User $user)
    {
        if($this->get('security.authorization_checker')->isGranted('EDIT', $user)){
            $em = $this->getDoctrine()->getManager();
            $em->detach($user);
            $liveUser = $this->getDoctrine()->getRepository('AppBundle:User')->findOneById($user->getId());
            $liveCurrentUser = $this->getDoctrine()->getRepository('AppBundle:User')->findOneById($this->getUser()->getId());
            if( ( $user->isOwnedByOrganization($liveCurrentUser->getOrganization()) and
                $liveUser->isOwnedByOrganization($liveCurrentUser->getOrganization()) ) or
                $this->get('security.authorization_checker')->isGranted('ROLE_DEV')
            ){
                if($user->newPassword){
                    $encoder = $this->container->get('security.password_encoder');
                    $encoded = $encoder->encodePassword($user, $user->newPassword);
                    $user->setPassword($encoded);
                }

                $liveUser = $em->merge($liveUser); //for cascading
                foreach($user->getUserRoles() as $userRole){
                    $userRole->setUser($liveUser);
                    $em->persist($userRole);
                }
                $user = $em->merge($user);

                $user->roleHierarchy = $this->get('security.role_hierarchy')->fetchRoleHierarchy();
                $em->flush();
                foreach($user->getUserRoles() as $userRole){
                    $this->updateAclByRoles($userRole, ['ROLE_USER'=>'view', 'ROLE_ADMIN'=>'operator']);
                }
                return $user;
            }else{
                throw $this->createNotFoundException('Organization #'.$user->getOrganization()->getId().' Not Found');
            }
        }else{
              throw $this->createAccessDeniedException();
        }
    }


    /**
     * @Rest\Delete("/user/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function deleteUserAction(\AppBundle\Entity\User $user)
    {
        if( $this->get('security.authorization_checker')->isGranted('DELETE', $user) and
            $user->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
            return $user;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Delete("/user_role/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function deleteUserRoleAction(\AppBundle\Entity\UserRole $userRole)
    {
        if( $this->get('security.authorization_checker')->isGranted('DELETE', $userRole) and
            $userRole->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            $em = $this->getDoctrine()->getManager();
            $em->remove($userRole);
            $em->flush();
            return [];
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Get("/role")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function listRoleAction(Request $request)
    {
        $page = (int)$request->query->get('page') - 1;
        $perPage =(int)$request->query->get('per_page');
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('COUNT(r.id)')
            ->from('AppBundle:Role', 'r');

        $totalItems = $qb->getQuery()->getSingleScalarResult();

        Utilities::setupSearchableEntityQueryBuild($qb, $request);

        $totalCount = $qb->getQuery()->getSingleScalarResult();

        $qb->select('r')
            ->orderBy('r.id', 'DESC')
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
     * @Rest\Get("/role/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getRoleAction(\AppBundle\Entity\Role $role)
    {
        if($this->get('security.authorization_checker')->isGranted('VIEW', $role)){
            return $role;
        }else{
            throw $this->createNotFoundException('Role #'.$role->getId().' Not Found');
        }
    }

    /**
     * @Rest\Get("/on_site_printer")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function listOnSitePrinterAction(Request $request)
    {
        $page = (int)$request->query->get('page') - 1;
        $perPage =(int)$request->query->get('per_page');
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('COUNT(osp.id)')
            ->from('AppBundle:OnSitePrinter', 'osp')
            ->where('osp.organization = :org')
            ->setParameter('org', $this->getUser()->getOrganization());

        $totalItems = $qb->getQuery()->getSingleScalarResult();

        Utilities::setupSearchableEntityQueryBuild($qb, $request);

        $totalCount = $qb->getQuery()->getSingleScalarResult();

        $qb->select('osp')
            ->orderBy('osp.id', 'DESC')
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
     * @Rest\Get("/on_site_printer/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getOnSitePrinterAction(\AppBundle\Entity\OnSitePrinter $onSitePrinter)
    {
        if( $this->get('security.authorization_checker')->isGranted('VIEW', $onSitePrinter) and
            $onSitePrinter->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            return $onSitePrinter;
        }else{
            throw $this->createNotFoundException('OnSitePrinter #'.$onSitePrinter->getId().' Not Found');
        }
    }

    /**
     * @Rest\Post("/on_site_printer")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("onSitePrinter", converter="fos_rest.request_body")
     */
    public function createOnSitePrinterAction(\AppBundle\Entity\OnSitePrinter $onSitePrinter)
    {
        if($this->get('security.authorization_checker')->isGranted('CREATE', $onSitePrinter)){
            $em = $this->getDoctrine()->getManager();
            $onSitePrinter->setOrganization($this->getUser()->getOrganization());
            $em->persist($onSitePrinter);
            $em->flush();
            $this->updateAclByRoles($onSitePrinter, ['ROLE_USER'=>'view', 'ROLE_ADMIN'=>'operator']);
            return $onSitePrinter;
        }else{
             throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Put("/on_site_printer/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("onSitePrinter", converter="fos_rest.request_body")
     */
    public function updateOnSitePrinterAction(\AppBundle\Entity\OnSitePrinter $onSitePrinter)
    {
        if($this->get('security.authorization_checker')->isGranted('EDIT', $onSitePrinter)){
            $em = $this->getDoctrine()->getManager();
            $em->detach($onSitePrinter);
            $liveOnSitePrinter = $this->getDoctrine()->getRepository('AppBundle:OnSitePrinter')->findOneById($onSitePrinter->getId());
            if( $onSitePrinter->isOwnedByOrganization($this->getUser()->getOrganization()) and
                $liveOnSitePrinter->isOwnedByOrganization($this->getUser()->getOrganization())
            ){
                $em->merge($onSitePrinter);
                $em->flush();
                return $onSitePrinter;
            }else{
                throw $this->createNotFoundException('Organization #'.$onSitePrinter->getOrganization()->getId().' Not Found');
            }
        }else{
             throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Delete("/on_site_printer/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function deleteOnSitePrinterAction(\AppBundle\Entity\OnSitePrinter $onSitePrinter)
    {
        if( $this->get('security.authorization_checker')->isGranted('DELETE', $onSitePrinter) and
            $onSitePrinter->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            $em = $this->getDoctrine()->getManager();
            $em->remove($onSitePrinter);
            $em->flush();
            return $onSitePrinter;
        }else{
             throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Get("/label")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function listLabelAction(Request $request)
    {
        $page = (int)$request->query->get('page') - 1;
        $perPage =(int)$request->query->get('per_page');
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('COUNT(l.id)')
            ->from('AppBundle:Label', 'l');

        $totalItems = $qb->getQuery()->getSingleScalarResult();

        Utilities::setupSearchableEntityQueryBuild($qb, $request);

        $totalCount = $qb->getQuery()->getSingleScalarResult();

        $qb->select('l')
            ->orderBy('l.id', 'DESC')
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
     * @Rest\Get("/label/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getLabelAction(\AppBundle\Entity\Label $label)
    {
        if($this->get('security.authorization_checker')->isGranted('VIEW', $label)){
            return $label;
        }else{
            throw $this->createNotFoundException('Label #'.$label->getId().' Not Found');
        }
    }

    /**
     * @Rest\Post("/label")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("label", converter="fos_rest.request_body")
     */
    public function createLabelAction(\AppBundle\Entity\Label $label)
    {
        if($this->get('security.authorization_checker')->isGranted('CREATE', $label)){
            $em = $this->getDoctrine()->getManager();
            $em->persist($label);
            $em->flush();
            $this->updateAclByRoles($label, ['ROLE_USER'=>'view', 'ROLE_ADMIN'=>'operator']);
            return $label;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Put("/label/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("label", converter="fos_rest.request_body")
     */
    public function updateLabelAction(\AppBundle\Entity\Label $label)
    {
        if($this->get('security.authorization_checker')->isGranted('EDIT', $label)){
            $em = $this->getDoctrine()->getManager();
            $em->merge($label);
            $em->flush();
            return $label;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Delete("/label/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function deleteLabelAction(\AppBundle\Entity\Label $label)
    {
        if($this->get('security.authorization_checker')->isGranted('DELETE', $label)){
            $em = $this->getDoctrine()->getManager();
            $em->remove($label);
            $em->flush();
            return $label;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

//Need to rework this I think


    /**
     * @Rest\Get("/label_on_site_printer")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function listLabelOnSitePrinterAction(Request $request)
    {
        $page = (int)$request->query->get('page') - 1;
        $perPage =(int)$request->query->get('per_page');
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('COUNT(losp.id)')
            ->from('AppBundle:LabelOnSitePrinter', 'losp')
            ->join('losp.')
            ->where('mi.organization = :org')
            ->setParameter('org', $this->getUser()->getOrganization());

        $totalItems = $qb->getQuery()->getSingleScalarResult();

        Utilities::setupSearchableEntityQueryBuild($qb, $request);

        $totalCount = $qb->getQuery()->getSingleScalarResult();

        $qb->select('losp')
            ->orderBy('losp.id', 'DESC')
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
     * @Rest\Get("/label_on_site_printer/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getLabelOnSitePrinterAction(\AppBundle\Entity\LabelOnSitePrinter $labelOnSitePrinter)
    {
        if( $this->get('security.authorization_checker')->isGranted('VIEW', $labelOnSitePrinter) and
            $labelOnSitePrinter->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            return $labelOnSitePrinter;
        }else{
            throw $this->createNotFoundException('LabelOnSitePrinter #'.$labelOnSitePrinter->getId().' Not Found');
        }
    }

    /**
     * @Rest\Post("/label_on_site_printer")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("labelOnSitePrinter", converter="fos_rest.request_body")
     */
    public function createLabelOnSitePrinterAction(\AppBundle\Entity\LabelOnSitePrinter $labelOnSitePrinter)
    {
        if( $this->get('security.authorization_checker')->isGranted('CREATE', $labelOnSitePrinter) and
            $labelOnSitePrinter->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            $em = $this->getDoctrine()->getManager();
            $em->persist($labelOnSitePrinter);
            $em->flush();
            $this->updateAclByRoles($labelOnSitePrinter, ['ROLE_USER'=>'view', 'ROLE_ADMIN'=>'operator']);
            return $labelOnSitePrinter;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Delete("/label_on_site_printer/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function deleteLabelOnSitePrinterAction(\AppBundle\Entity\LabelOnSitePrinter $labelOnSitePrinter)
    {
        if( $this->get('security.authorization_checker')->isGranted('DELETE', $labelOnSitePrinter) and
            $labelOnSitePrinter->isOwnedByOrganization($this->getUser()->getOrganization())
        ){
            $em = $this->getDoctrine()->getManager();
            $em->remove($labelOnSitePrinter);
            $em->flush();
            return $labelOnSitePrinter;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Post("/organization/{id}/upload_image", )
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function uploadImageAction(\AppBundle\Entity\Organization $organization, Request $request)
    {
        if( $this->get('security.authorization_checker')->isGranted('EDIT', $organization) and
            (   $this->getUser()->getOrganization() === $organization or
                $this->get('security.authorization_checker')->isGranted('ROLE_DEV')     )
        ){
            $imageFileUpload = $request->files->get('image');
            if($imageFileUpload->isValid()){
                $image = new \AppBundle\Entity\UploadedImage();
                $image->setOrganization($organization);
                $image->setName($imageFileUpload->getBasename());
                $image->setMimeType($imageFileUpload->getMimeType());
                $imageData = file_get_contents($imageFileUpload);
                $imageSize = getimagesizefromstring($imageData);
                $image->setWidth($imageSize[0]);
                $image->setHeight($imageSize[1]);
                $image->setData($imageData);
                $em = $this->getDoctrine()->getManager();
                $em->persist($image);
                $em->flush();
                $this->updateAclByRoles($image, ['ROLE_USER'=>'view', 'ROLE_ADMIN'=>'operator']);
                return $image;
            }else{
                 throw new HttpException(500, UploadException::codeToMessage($imageFileUpload->getError()) );
            }
        }else{
             throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Get("/image/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getImageAction(\AppBundle\Entity\Image $image)
    {
        if( $this->get('security.authorization_checker')->isGranted('VIEW', $image) and
            (   $image->isOwnedByOrganization($this->getUser()->getOrganization()) or
                $this->get('security.authorization_checker')->isGranted('ROLE_DEV')     )
        ){
            return $image;
        }else{
            throw $this->createNotFoundException('Image #'.$image->getId().' Not Found');
        }
    }

    /**
     * @Rest\Get("/image/{id}/src", )
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getImageSrcAction(\AppBundle\Entity\UploadedImage $image)
    {
        if( $this->get('security.authorization_checker')->isGranted('EDIT', $image) and
            (   $image->isOwnedByOrganization($this->getUser()->getOrganization()) or
                $this->get('security.authorization_checker')->isGranted('ROLE_DEV')     )
        ){
            $response = new Response();
            rewind($image->getData());
            $response->setContent(stream_get_contents($image->getData()));
            $response->headers->set('Content-Type', $image->getMimeType());
            return $response;
        }else{
             throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Get("/help_topic")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function listHelpTopicAction(Request $request)
    {
        $page = (int)$request->query->get('page') - 1;
        $perPage =(int)$request->query->get('per_page');
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('COUNT(e.id)')
            ->from('AppBundle:HelpTopic', 'e');


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
     * @Rest\Get("/help_topic/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getHelpTopicAction(\AppBundle\Entity\HelpTopic $helpTopic)
    {
        if( $this->get('security.authorization_checker')->isGranted('VIEW', $helpTopic)){
            return $helpTopic;
        }else{
            throw $this->createNotFoundException('Help Topic #'.$helpTopic->getId().' Not Found');
        }
    }

    /**
     * @Rest\Post("/help_topic")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("helpTopic", converter="fos_rest.request_body")
     */
    public function createHelpTopicAction(\AppBundle\Entity\HelpTopic $helpTopic)
    {
        if($this->get('security.authorization_checker')->isGranted('CREATE', $helpTopic)){
            $em = $this->getDoctrine()->getManager();
            $em->persist($helpTopic);
            $em->flush();
            $this->updateAclByRoles($helpTopic, ['ROLE_USER'=>'view', 'ROLE_DEV'=>'operator']);
            return $helpTopic;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Put("/help_topic/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("helpTopic", converter="fos_rest.request_body")
     */
    public function updateHelpTopicAction(\AppBundle\Entity\HelpTopic $helpTopic)
    {
        if($this->get('security.authorization_checker')->isGranted('EDIT', $helpTopic)){
            $em = $this->getDoctrine()->getManager();
            $em->merge($helpTopic);
            $em->flush();
            return $helpTopic;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Delete("/help_topic/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function deleteHelpTopicAction(\AppBundle\Entity\HelpTopic $helpTopic)
    {
        if( $this->get('security.authorization_checker')->isGranted('DELETE', $helpTopic)){
            $em = $this->getDoctrine()->getManager();
            $em->remove($helpTopic);
            $em->flush();
            return $helpTopic;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

}
//nohup php app/console thruway:process start &
//curl -v -H "Accept: application/json" -H "Content-type: application/json" -X POST -d '{"name":"DFW"}' http://localhost/~belac/step-inventory/app_dev.php/office
//curl -v -H "Accept: application/json" -H "PHP_AUTH_USER: admintest" -H "PHP_AUTH_PW: password" http://localhost/~belac/step-inventory/app_dev.php/office