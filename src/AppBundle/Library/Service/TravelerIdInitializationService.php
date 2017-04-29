<?php
namespace AppBundle\Library\Service;

use AppBundle\Entity\TravelerId;
use AppBundle\Entity\Unit;
use AppBundle\Entity\UnitType;

class TravelerIdInitializationService
{
    use \Symfony\Component\DependencyInjection\ContainerAwareTrait;
    use \AppBundle\Controller\Mixin\UpdateAclMixin;

    public function initialize(TravelerId $tid)
    {
        $createdEnities = [];
        if($tid->getUnit()){
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            $tid->getUnit()->setTravelerId($tid);
            foreach($tid->getUnit()->getProperties() as $unitProperty){
                $unitProperty->setUnit($tid->getUnit());
            }
            $tid->getUnit()->setOrganization($user->getOrganization());
            $createdEnities[] = $tid->getUnit();
        }
        return $createdEnities;
    }

}