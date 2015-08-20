<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FOS\RestBundle\Controller\Annotations AS Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Doctrine\Common\Collections\ArrayCollection;


class DefaultRestController extends FOSRestController
{
   
    /**
     * @Rest\Get("/tid")
     * @Rest\View(template=":default:list_travelerid.html.twig",serializerEnableMaxDepthChecks=true)
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
     * @Rest\View(template=":default:show_travelerid.html.twig",serializerEnableMaxDepthChecks=true)
     */
    public function showTravelerIdAction(\AppBundle\Entity\TravelerId $travelerId)
    {
        return $travelerId;
    }

    /**
     * @Rest\Post("/tid")
     * @Rest\View(template=":default:create_travelerid.html.twig",serializerEnableMaxDepthChecks=true)
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
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true)
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
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true)
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
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true)
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
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true)
     */
    public function showDepartmentAction(\AppBundle\Entity\Department $department)
    {
        return $department;
    }

    /**
     * @Rest\Post("/department")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true)
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
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true)
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
   	
        return array('list'=>$offices);
    }

    /**
     * @Rest\Get("/office/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true)
     */
    public function showOfficeAction(\AppBundle\Entity\Office $office)
    {
        return $office;
    }

    /**
     * @Rest\Post("/office")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true)
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
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true)
     */
    public function listMenuItemAction()
    {
        $items = $this->getDoctrine()
        ->getRepository('AppBundle:MenuItem')
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
     * @Rest\Get("/menu_item/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true)
     */
    public function showMenuItemAction(\AppBundle\Entity\MenuItem $menuItem)
    {
        return $menuItem;
    }

    /**
     * @Rest\Post("/menu_item")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true)
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
     * @Rest\Get("/user")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true)
     */
    public function listUserAction()
    {
        $filter = $this->getRequest()->query->all();
        $items = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->searchUsers($filter);

        $itemlist = array();
        $authorizationChecker = $this->get('security.authorization_checker');
        foreach($items as $item){
            if (true === $authorizationChecker->isGranted('VIEW', $item)) {
                $itemlist[] = $item;
            }
        }
    
        return array('list'=>$items);
    }

    /**
     * @Rest\Get("/user/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true)
     */
    public function showUserAction(\AppBundle\Entity\User $user)
    {
        return $user;
    }

    /**
     * @Rest\Post("/user")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true)
     * @ParamConverter("user", converter="fos_rest.request_body")
     */
    public function createUserAction(\AppBundle\Entity\User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        return $user;
    }

    /**
     * @Rest\Put("/user/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true)
     * @ParamConverter("user", converter="fos_rest.request_body")
     */
    public function updateUserAction(\AppBundle\Entity\User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $em->merge($user);
        $em->flush();
        return $user;
    }

     /**
     * @Rest\Patch("/user/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true)
     * @ParamConverter("user", converter="fos_rest.request_body")
     */
    public function patchUserAction(\AppBundle\Entity\User $user, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $liveUser = $em->getRepository('AppBundle:User')->findOneById($id);
        $this->patchEntity($liveUser, $user);
        $em->flush();
        $this->pushUpdate($liveUser);
        return $user;
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