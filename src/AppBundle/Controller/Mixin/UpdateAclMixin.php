<?php

namespace AppBundle\Controller\Mixin;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

trait UpdateAclMixin
{
    public function updateAclByRoles($entity, $roleMasksMap)
    {
        $aclProvider = $this->container->get('security.acl.provider');

        $objectIdentity = ObjectIdentity::fromDomainObject($entity);
        $acl = $aclProvider->createAcl($objectIdentity);

        foreach($roleMasksMap as $role => $maskNames){
            $roleSecurityIdentity = new RoleSecurityIdentity($role);
            $maskBuilder = new MaskBuilder();
            foreach($maskNames as $maskName){
                $maskBuilder->add($maskName);
            }
            $acl->insertObjectAce($roleSecurityIdentity, $builder->get());
        }

        $aclProvider->updateAcl($acl);
    }
}