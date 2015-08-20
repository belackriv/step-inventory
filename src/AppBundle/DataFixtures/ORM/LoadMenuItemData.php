<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use AppBundle\Entity\MenuItem;

class LoadMenuItemData extends AbstractFixture implements DependentFixtureInterface, ContainerAwareInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {

        $refs = $this->referenceRepository->getReferences();
        $deptRefNames = array();
        $linkRefNames = array();

        foreach($refs as $ref){
            $refNames = $this->referenceRepository->getReferenceNames($ref);
            if(is_a($ref, 'AppBundle\Entity\Department')){
                $deptRefNames[] = $refNames[0];
            }
            if(is_a($ref, 'AppBundle\Entity\MenuLink')){
                $linkRefNames[] = $refNames[0];
            }
        }
        $items = array();
        foreach($deptRefNames as $deptRefName){
            $i=1;
            foreach($linkRefNames as $linkRefName){ 
                $item = new MenuItem();
                $item->isActive(true);
                $item->setPosition($i);
                $item->setDepartment($this->getReference($deptRefName));
                $item->setMenuLink($this->getReference($linkRefName));
                if(in_array($linkRefName, array('leadLink','userLink'))){
                    $item->setParent($items[$deptRefName]['mainLink']);
                }
                $manager->persist($item);
                $items[$deptRefName][$linkRefName] = $item;
                $i++;
            }
        }

        $manager->flush();

    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies()
    {
        return array('AppBundle\DataFixtures\ORM\LoadMenuLinkData','AppBundle\DataFixtures\ORM\LoadDepartmentData'); // fixture classes fixture is dependent on
    }

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

}