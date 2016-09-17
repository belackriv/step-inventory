<?php

namespace AppBundle\Controller\Mixin;

use AppBundle\Library\Service\AclUpdaterService;

trait UpdateAclMixin
{
    public function updateAclByRoles($entity, $roleMasksMap)
    {
        $aclUpdaterService = new AclUpdaterService($this->container);
        $aclUpdaterService->updateAclByRoles($entity, $roleMasksMap);
    }
}