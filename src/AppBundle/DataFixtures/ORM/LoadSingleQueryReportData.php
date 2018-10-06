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
use Symfony\Component\ClassLoader\ClassMapGenerator;
use AppBundle\Entity\SingleQueryReport;
use AppBundle\Entity\SingleQueryReportRole;
use AppBundle\Entity\SingleQueryReportPart;
use AppBundle\Entity\SingleQueryReportCountPart;
use AppBundle\Entity\SingleQueryReportParameter;
use AppBundle\Entity\SingleQueryReportParameterPart;

class LoadSingleQueryReportData extends AbstractFixture implements ContainerAwareInterface
{
    private $aclRoles = [];

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


    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
       //load new entities
        $this->initAclRoles();
        $reports = [];
        foreach($this->getEntityData() as $data){
            $singleQueryReport = $manager->getRepository('AppBundle:SingleQueryReport')
                ->findOneBy(['name'=> $data['name']]);
            if($singleQueryReport){
                foreach($singleQueryReport->getSingleQueryReportRoles() as $role){
                    $manager->remove($role);
                }
                foreach($singleQueryReport->getParts() as $part){
                    $manager->remove($part);
                }
                foreach($singleQueryReport->getCountParts() as $part){
                    $manager->remove($part);
                }
                foreach($singleQueryReport->getSingleQueryReportParameters() as $param){
                    foreach($param->getParts() as $part){
                        $manager->remove($part);
                    }
                    $manager->remove($param);
                }
                $manager->remove($singleQueryReport);
            }
            $singleQueryReport = new SingleQueryReport();
            $singleQueryReport->setTag($data['tag']);
            $singleQueryReport->setName($data['name']);
            $singleQueryReport->setDescription($data['description']);
            $singleQueryReport->setFilename($data['filename']);
            $availableToAccounts = isset($data['availableToAccounts'])?$data['availableToAccounts']:false;
            $singleQueryReport->setColumns($data['columns']);
            $singleQueryReport->setParameterWhiteList($data['parameterWhiteList']);

            foreach($data['roles'] as $roleRole){
                $role = new SingleQueryReportRole();
                $role->setRole($this->getReference($roleRole));
                $singleQueryReport->addSingleQueryReportRole($role);
            }
            foreach($data['parts'] as $index => $partData){
                $part = new SingleQueryReportPart();
                $part->setMethodName($partData['methodName']);
                $part->setArgs($partData['args']);
                $part->setOrder($index);
                $singleQueryReport->addPart($part);
            }
            foreach($data['countParts'] as $index => $partData){
                $part = new SingleQueryReportCountPart();
                $part->setMethodName($partData['methodName']);
                $part->setArgs($partData['args']);
                $part->setOrder($index);
                $singleQueryReport->addCountPart($part);
            }
            foreach($data['singleQueryReportParameters'] as $paramData){
                $paramter = new SingleQueryReportParameter();
                $paramter->setName($paramData['name']);
                $paramter->setTitle($paramData['title']);
                $paramter->setPriority($paramData['priority']);
                $paramter->setType($paramData['type']);
                $paramter->setIsFuzzy($paramData['isFuzzy']);
                $paramter->setIsHidden($paramData['isHidden']);
                $paramter->setIsOptional($paramData['isOptional']);
                $paramter->setTemplate($paramData['template']);
                $paramter->setValue($paramData['value']);
                $paramter->setChoicesPropertyName($paramData['choicesPropertyName']);
                foreach($paramData['parts'] as $index => $partData){
                    $part = new SingleQueryReportParameterPart();
                    $part->setMethodName($partData['methodName']);
                    $part->setArgs($partData['args']);
                    $part->setOrder($index);
                    $paramter->addPart($part);
                }
                $singleQueryReport->addSingleQueryReportParameter($paramter);
            }
            $manager->persist($singleQueryReport);

            $reports[] = $singleQueryReport;
        }
        $manager->flush();

        foreach($reports as $report){
            $this->addAcl($report);
            foreach($report->getParts() as $part){
                $this->addAcl($part);
            }
            foreach($report->getCountParts() as $part){
                $this->addAcl($part);
            }
            foreach($report->getSingleQueryReportParameters() as $paramter){
                $this->addAcl($paramter);
                foreach($paramter->getParts() as $part){
                    $this->addAcl($part);
                }
            }
        }
        return true;
    }

    public function initAclRoles()
    {
        $aclProvider = $this->container->get('security.acl.provider');
        $devRoleSecurityIdentity = new RoleSecurityIdentity('ROLE_DEV');
        $adminRoleSecurityIdentity = new RoleSecurityIdentity('ROLE_ADMIN');
        $leadRoleSecurityIdentity = new RoleSecurityIdentity('ROLE_LEAD');
        $userRoleSecurityIdentity = new RoleSecurityIdentity('ROLE_USER');

        $this->aclRoles['ROLE_DEV'] = $devRoleSecurityIdentity;
        $this->aclRoles['ROLE_ADMIN'] = $adminRoleSecurityIdentity;
        $this->aclRoles['ROLE_LEAD'] = $leadRoleSecurityIdentity;
        $this->aclRoles['ROLE_USER'] = $userRoleSecurityIdentity;

    }

    public function addAcl($entity)
    {
        $aclProvider = $this->container->get('security.acl.provider');
        $objectIdentity = ObjectIdentity::fromDomainObject($entity);
        try {
            $acl = $aclProvider->findAcl($objectIdentity);
        } catch (\Symfony\Component\Security\Acl\Exception\AclNotFoundException $e) {
            $acl = $aclProvider->createAcl($objectIdentity);
            $acl->insertObjectAce($this->aclRoles['ROLE_USER'], MaskBuilder::MASK_VIEW);
            $acl->insertObjectAce($this->aclRoles['ROLE_DEV'], MaskBuilder::MASK_OPERATOR);
            $aclProvider->updateAcl($acl);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies()
    {
        return ['AppBundle\DataFixtures\ORM\LoadRoleData']; // fixture classes fixture is dependent on
    }

    public function getEntityData(){
        $classMap = ClassMapGenerator::createMap(__DIR__.'/SingleQueryReports');
        $data = [];
        foreach($classMap as $className => $filePath){
            $data[] = $className::REPORT_DATA;
        }
        return $data;
      }
}