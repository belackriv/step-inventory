<?php
namespace AppBundle\Library\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

class AclUpdaterService extends AbstractService
{
	public function updateAclByRoles($entity, $roleMasksMap)
    {
        $aclProvider = $this->container->get('security.acl.provider');

        $objectIdentity = ObjectIdentity::fromDomainObject($entity);

        try {
        	$acl = $aclProvider->findAcl($objectIdentity);
        } catch (\Symfony\Component\Security\Acl\Exception\AclNotFoundException $e) {
        	$acl = $aclProvider->createAcl($objectIdentity);
        }

        foreach($roleMasksMap as $role => $maskNames){
            $roleSecurityIdentity = new RoleSecurityIdentity($role);
            $maskBuilder = new MaskBuilder();
            $maskNamesArray = is_array($maskNames)?$maskNames:[$maskNames];
            foreach($maskNamesArray as $maskName){
                $maskBuilder->add($maskName);
            }
            $acl->insertObjectAce($roleSecurityIdentity, $maskBuilder->get());
        }

        $aclProvider->updateAcl($acl);
    }

}