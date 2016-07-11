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
use Doctrine\Common\Collections\ArrayCollection;


class DefaultRestController extends FOSRestController
{

    /**
     * @Rest\Get("/tid")
     * @Rest\View(template=":default:list_travelerid.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function listTravelerIdAction()
    {
    	 $items = $this->getDoctrine()
        ->getRepository('AppBundle:TravelerId')
        ->findAll();

        return array('list'=>$items);
    }

    /**
     * @Rest\Get("/tid/{id}")
     * @Rest\View(template=":default:get_travelerid.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getTravelerIdAction(\AppBundle\Entity\TravelerId $travelerId)
    {
        return $travelerId;
    }

    /**
     * @Rest\Post("/tid")
     * @Rest\View(template=":default:create_travelerid.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("travelerId", converter="fos_rest.request_body")
     */
    public function createTravelerIdAction(\AppBundle\Entity\TravelerId $travelerId)
    {
    	$em = $this->getDoctrine()->getManager();
	    $em->persist($travelerId);
	    $em->flush();
        return $travelerId;
    }

    /**
     * @Rest\Put("/tid/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("travelerId", converter="fos_rest.request_body")
     */
    public function updateTravelerIdAction(\AppBundle\Entity\TravelerId $travelerId)
    {
        $em = $this->getDoctrine()->getManager();
        $em->merge($travelerId);
        $em->flush();
        return $travelerId;
    }

     /**
     * @Rest\Patch("/tid/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("travelerId", converter="fos_rest.request_body")
     */
    public function patchTravelerIdAction(\AppBundle\Entity\TravelerId $travelerId, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $liveTravelerId = $em->getRepository('AppBundle:TravelerId')->findOneById($id);
        $this->patchEntity($liveTravelerId, $travelerId);
        $em->flush();
        $this->pushUpdate($liveTravelerId);
        return $travelerId;
    }

    /**
     * @Rest\Get("/department")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function listDepartmentAction()
    {
        $items = $this->getDoctrine()
        ->getRepository('AppBundle:Department')
        ->findAll();

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
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getDepartmentAction(\AppBundle\Entity\Department $department)
    {
        return $department;
    }

    /**
     * @Rest\Post("/department")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("department", converter="fos_rest.request_body")
     */
    public function createDepartmentAction(\AppBundle\Entity\Department $department)
    {
    	$em = $this->getDoctrine()->getManager();
	    $em->persist($department);
	    $em->flush();
        return $department;
    }

    /**
     * @Rest\Get("/office")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default", "ListOffices"})
     */
    public function listOfficeAction()
    {
        $offices = $this->getDoctrine()
        ->getRepository('AppBundle:Office')
        ->findAll();

        $authorizationChecker = $this->get('security.authorization_checker');
        foreach($offices as $office){
            foreach($office->getDepartments() as $dept){
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

        return ['total_count'=> count($offices), 'total_items' => count($offices), 'list'=>$offices];
    }

    /**
     * @Rest\Get("/office/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getOfficeAction(\AppBundle\Entity\Office $office)
    {
        return $office;
    }

    /**
     * @Rest\Post("/office")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("office", converter="fos_rest.request_body")
     */
    public function createOfficeAction(\AppBundle\Entity\Office $office)
    {
    	$em = $this->getDoctrine()->getManager();
	    $em->persist($office);
	    $em->flush();
        return $office;
    }

    /**
     * @Rest\Get("/menu_item")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function listMenuItemAction(Request $request)
    {
        $page = (int)$request->query->get('page') - 1;
        $perPage =(int)$request->query->get('per_page');
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('COUNT(mi.id)')
            ->from('AppBundle:MenuItem', 'mi');

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
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getMenuItemAction(\AppBundle\Entity\MenuItem $menuItem)
    {
        return $menuItem;
    }

    /**
     * @Rest\Post("/menu_item")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("menuItem", converter="fos_rest.request_body")
     */
    public function createMenuItemAction(\AppBundle\Entity\MenuItem $menuItem)
    {
    	$em = $this->getDoctrine()->getManager();
	    $em->persist($menuItem);
	    $em->flush();
        return $menuItem;
    }

    /**
     * @Rest\Put("/menu_item/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("menuItem", converter="fos_rest.request_body")
     */
    public function updateMenuItemAction(\AppBundle\Entity\MenuItem $menuItem)
    {
        $em = $this->getDoctrine()->getManager();
        $em->merge($menuItem);
        $em->flush();
        return $menuItem;
    }

    /**
     * @Rest\Delete("/menu_item/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function deleteMenuItemAction(\AppBundle\Entity\MenuItem $menuItem)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($menuItem);
        $em->flush();
        return $role;
    }

    /**
     * @Rest\Get("/menu_link")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function listMenuLinkAction(Request $request)
    {
        $items = $this->getDoctrine()
        ->getRepository('AppBundle:MenuLink')
        ->findAll();

        $itemlist = array();
        $authorizationChecker = $this->get('security.authorization_checker');
        foreach($items as $item){
            if (true === $authorizationChecker->isGranted('VIEW', $item)) {
                $itemlist[] = $item;
            }
        }

        return ['total_count'=> count($itemlist), 'total_itemlist' => count($itemlist), 'list'=>$itemlist];
    }

    /**
     * @Rest\Get("/menu_link/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getMenuLinkAction(\AppBundle\Entity\MenuLink $menuLink)
    {
        return $menuLink;
    }




    /**
     * @Rest\Get("/myself")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default", "GetMyself"})
     */
    public function getMyself(Request $request)
    {
        $session = $request->getSession();
        $myself = $this->getUser();
        if($session->get('currentDepartmentId')){
            $department = $this->getDoctrine() ->getRepository('AppBundle:DepartMent')
                ->find($session->get('currentDepartmentId'));
            $myself->currentDepartment = $department;
        }else{
            $myself->currentDepartment = $myself->getDefaultDepartment();
        }
        $myself->appMessage = 'Test Message';
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
            ->from('AppBundle:User', 'u');

        $totalItems = $qb->getQuery()->getSingleScalarResult();

        Utilities::setupSearchableEntityQueryBuild($qb, $request);

        $totalCount = $qb->getQuery()->getSingleScalarResult();

        $qb->select('u')
            ->orderBy('u.id', 'DESC')
            ->setMaxResults($perPage)
            ->setFirstResult($page*$perPage);

        $items = $qb->getQuery()->getResult();

        return ['total_count'=> (int)$totalCount, 'total_items' => (int)$totalItems, 'list'=>$items];
    }

    /**
     * @Rest\Get("/user/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getUserAction(\AppBundle\Entity\User $user)
    {
        return $user;
    }

    /**
     * @Rest\Post("/user")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("user", converter="fos_rest.request_body")
     */
    public function createUserAction(\AppBundle\Entity\User $user)
    {
        $em = $this->getDoctrine()->getManager();

        $encoder = $this->container->get('security.password_encoder');
        $encoded = $encoder->encodePassword($user, $user->getPassword());
        $user->setPassword($encoded);

        $em->persist($user);
        foreach($user->getUserRoles() as $userRole){
            $userRole->setUser($user);
            $em->persist($userRole);
        }
        $em->flush();
        return $user;
    }

    /**
     * @Rest\Put("/user/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("user", converter="fos_rest.request_body")
     */
    public function updateUserAction(\AppBundle\Entity\User $user)
    {
        $em = $this->getDoctrine()->getManager();

        $encoder = $this->container->get('security.password_encoder');
        $encoded = $encoder->encodePassword($user, $user->getPassword());
        $user->setPassword($encoded);

        $em->merge($user);
        foreach($user->getUserRoles() as $userRole){
            $userRole->setUser($user);
            $em->persist($userRole);
        }
        $em->flush();
        return $user;
    }

     /**
     * @Rest\Patch("/user/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("user", converter="fos_rest.request_body")
     */
    public function patchUserAction(\AppBundle\Entity\User $user, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $liveUser = $em->getRepository('AppBundle:User')->findOneById($id);
        $this->patchEntity($liveUser, $user);

        $encoder = $this->container->get('security.password_encoder');
        $encoded = $encoder->encodePassword($liveUser, $liveUser->getPassword());
        $liveUser->setPassword($encoded);

        $em->flush();
        $this->pushUpdate($liveUser);
        return $user;
    }

    /**
     * @Rest\Delete("/user_role/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function deleteUserRoleAction(\AppBundle\Entity\UserRole $userRole)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($userRole);
        $em->flush();
        return $role;
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

        return ['total_count'=> (int)$totalCount, 'total_items' => (int)$totalItems, 'list'=>$items];
    }

    /**
     * @Rest\Get("/role/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getRoleAction(\AppBundle\Entity\Role $role)
    {
        return $role;
    }


    private function patchEntity($entity, $patch)
    {

        $reflectedObject = new \ReflectionObject($patch);
        $reflectedProperties = $reflectedObject->getProperties(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED);
        foreach($reflectedProperties as $property){
            $property->setAccessible(true);
            $propertyValue = $property->getValue($patch);
            if( $propertyValue !== null){
                $setMethodName = 'set'.$property->getName();
                $entity->$setMethodName($propertyValue);
            }
        }
    }

    private function pushUpdate($entity)
    {
        $client = $this->container->get('thruway.client');
        $serializer = $this->get('jms_serializer');

        $entityReflectiion = new \ReflectionClass(get_class($entity));
        $classShortName = strtolower($entityReflectiion->getShortName());
        $json = $serializer->serialize($entity, 'json');

        $client->publish("com.stepthrough."+$classShortName, [$json]);
    }
}
//nohup php app/console thruway:process start &
//curl -v -H "Accept: application/json" -H "Content-type: application/json" -X POST -d '{"name":"DFW"}' http://localhost/~belac/stepthrough/app_dev.php/office
//curl -v -H "Accept: application/json" -H "PHP_AUTH_USER: admintest" -H "PHP_AUTH_PW: password" http://localhost/~belac/stepthrough/app_dev.php/office