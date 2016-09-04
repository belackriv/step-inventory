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


class SerializedInventoryRestController extends FOSRestController
{

    use Mixin\RestPatchMixin;
    use Mixin\UpdateAclMixin;
    use Mixin\WampUpdatePusher;
    use Mixin\TravelerIdLogMixin;

    /**
     * @Rest\Get("/tid")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function listTravelerIdAction(Request $request)
    {
        $page = (int)$request->query->get('page') - 1;
        $perPage =(int)$request->query->get('per_page');
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('COUNT(tid.id)')
            ->from('AppBundle:TravelerId', 'tid');

        $totalItems = $qb->getQuery()->getSingleScalarResult();

        Utilities::setupSearchableEntityQueryBuild($qb, $request);

        $totalCount = $qb->getQuery()->getSingleScalarResult();

        $qb->select('tid')
            ->orderBy('tid.id', 'DESC')
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
     * @Rest\Get("/tid/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function getTravelerIdAction(\AppBundle\Entity\TravelerId $travelerId)
    {
        if($this->get('security.authorization_checker')->isGranted('VIEW', $travelerId)){
            return $travelerId;
        }else{
            throw $this->createNotFoundException('TravelerId #'.$travelerId->getId().' Not Found');
        }
    }

    /**
     * @Rest\Post("/tid")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("travelerId", converter="fos_rest.request_body")
     */
    public function createTravelerIdAction(\AppBundle\Entity\TravelerId $travelerId)
    {
        if($this->get('security.authorization_checker')->isGranted('CREATE', $travelerId)){
            $em = $this->getDoctrine()->getManager();
            $em->persist($travelerId);
            $travelerId->generateLabel();
            $em->flush();
            $this->updateAclByRoles($travelerId, ['ROLE_USER'=>['view', 'edit'], 'ROLE_ADMIN'=>'operator']);
            return $travelerId;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Put("/tid/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("travelerId", converter="fos_rest.request_body")
     */
    public function updateTravelerIdAction(\AppBundle\Entity\TravelerId $travelerId, $id)
    {

        if($this->get('security.authorization_checker')->isGranted('EDIT', $travelerId)){
            $em = $this->getDoctrine()->getManager();
            $em->detach($travelerId);
            $liveTravelerId = $em->getRepository('AppBundle:TravelerId')->findOneById($id);
            $edit = $this->checkForTravelerIdEdit($liveTravelerId, $travelerId);
            $move = $this->checkForTravelerIdMovement($liveTravelerId, $travelerId);
            $em->merge($travelerId);
            $em->flush();
            if($edit){
                $this->updateAclByRoles($edit, ['ROLE_USER'=>['view', 'edit'], 'ROLE_ADMIN'=>'operator']);
            }
            if($move){
                $this->updateAclByRoles($move, ['ROLE_USER'=>['view', 'edit'], 'ROLE_ADMIN'=>'operator']);
            }
            return $travelerId;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

     /**
     * @Rest\Patch("/tid/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("travelerId", converter="fos_rest.request_body")
     */
    public function patchTravelerIdAction(\AppBundle\Entity\TravelerId $travelerId, $id)
    {
        if($this->get('security.authorization_checker')->isGranted('EDIT', $travelerId)){
            $em = $this->getDoctrine()->getManager();
            $em->detach($travelerId);
            $liveTravelerId = $em->getRepository('AppBundle:TravelerId')->findOneById($id);
            $edit = $this->checkForTravelerIdEdit($liveTravelerId, $travelerId);
            $move = $this->checkForTravelerIdMovement($liveTravelerId, $travelerId);
            $this->patchEntity($liveTravelerId, $travelerId);
            $em->flush();
            if($edit){
                $this->updateAclByRoles($edit, ['ROLE_USER'=>['view', 'edit'], 'ROLE_ADMIN'=>'operator']);
            }
            if($move){
                $this->updateAclByRoles($move, ['ROLE_USER'=>['view', 'edit'], 'ROLE_ADMIN'=>'operator']);
            }
            return $travelerId;
        }else{
            throw $this->createAccessDeniedException();
        }
    }

    /**
     * @Rest\Delete("/tid/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     */
    public function deleteTravelerIdAction(\AppBundle\Entity\TravelerId $travelerId)
    {
        if($this->get('security.authorization_checker')->isGranted('DELETE', $travelerId)){
            $em = $this->getDoctrine()->getManager();
            $em->remove($travelerId);
            $em->flush();
            return $travelerId;
        }else{
            throw $this->createAccessDeniedException();
        }
    }


    /**
     * @Rest\Post("/mass_tid")
     * @Rest\View(template=":default:create_travelerid.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("massTravelerId", converter="fos_rest.request_body")
     */
    public function createMassTravelerIdAction(\AppBundle\Entity\MassTravelerId $massTravelerId)
    {
        set_time_limit(300);
        ini_set('memory_limit','1024M');
        $em = $this->getDoctrine()->getManager();
        foreach($massTravelerId->getTravelerIds() as $travelerId){
            if($this->get('security.authorization_checker')->isGranted('CREATE', $travelerId)){
                $em->persist($travelerId);
                $travelerId->generateLabel();
            }else{
                throw $this->createAccessDeniedException();
            }
        }
        $em->flush();
        foreach($massTravelerId->getTravelerIds() as $travelerId){
            $this->updateAclByRoles($travelerId, ['ROLE_USER'=>['view', 'edit'], 'ROLE_ADMIN'=>'operator']);
        }
        return $massTravelerId;
    }

    /**
     * @Rest\Put("/mass_tid/{id}")
     * @Rest\View(template=":default:index.html.twig",serializerEnableMaxDepthChecks=true, serializerGroups={"Default"})
     * @ParamConverter("massTravelerId", converter="fos_rest.request_body")
     */
    public function updateMassTravelerIdAction(\AppBundle\Entity\MassTravelerId $massTravelerId)
    {
        set_time_limit(300);
        ini_set('memory_limit','1024M');
        $em = $this->getDoctrine()->getManager();
        $travelerIdLogEntities = [];
        foreach($massTravelerId->getTravelerIds() as $travelerId){
            if($this->get('security.authorization_checker')->isGranted('EDIT', $travelerId)){
                $em->detach($travelerId);
                $liveTravelerId = $em->getRepository('AppBundle:TravelerId')->findOneById($travelerId->getId());
                $edit = $this->checkForTravelerIdEdit($liveTravelerId, $travelerId);
                $move = $this->checkForTravelerIdMovement($liveTravelerId, $travelerId);
                if($edit){
                    $travelerIdLogEntities[] = $edit;
                }
                if($move){
                    $travelerIdLogEntities[] = $move;
                }
                $em->merge($travelerId);
            }else{
                throw $this->createAccessDeniedException();
            }
        }
        $em->flush();
        foreach($travelerIdLogEntities as $logEntity){
            $this->updateAclByRoles($logEntity, ['ROLE_USER'=>['view', 'edit'], 'ROLE_ADMIN'=>'operator']);
        }
        return $massTravelerId;
    }

}