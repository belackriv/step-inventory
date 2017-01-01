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
use AppBundle\Entity\SingleQueryReport;
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
        $reports = [];
        foreach($this->getEntityData() as $data){
            $singleQueryReport = $manager->getRepository('AppBundle:SingleQueryReport')
                ->findOneBy(['name'=>$data['name']]);
            if(!$singleQueryReport){
                $singleQueryReport = new SingleQueryReport();
                $singleQueryReport->setTag($data['tag']);
                $singleQueryReport->setName($data['name']);
                $singleQueryReport->setFilename($data['filename']);
                $availableToAccounts = isset($data['availableToAccounts'])?$data['availableToAccounts']:false;
                $singleQueryReport->setColumns($data['columns']);
                $singleQueryReport->setParameterWhiteList($data['parameterWhiteList']);
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
            }
            $reports[] = $singleQueryReport;
            //$this->addReference('dfwOffice', $dfwOffice);
        }
        $manager->flush();

        $this->initAclRoles();

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
        $acl = $aclProvider->createAcl($objectIdentity);
        $acl->insertObjectAce($this->aclRoles['ROLE_USER'], MaskBuilder::MASK_VIEW);
        $acl->insertObjectAce($this->aclRoles['ROLE_DEV'], MaskBuilder::MASK_OPERATOR);
        $aclProvider->updateAcl($acl);
    }

    public function getEntityData(){
        return [
            //#3
            [
                'tag'   => 'bin,client,ion',
                'name'  => 'TID Count By Bin, Client, and ION',
                'filename'  => 'tid_bin_client_ion_count',
                'parts' => [
                    [
                        'methodName' => 'select',
                        'args' => ["b.name bname", "c.name cname", "i.label ilabel", "COUNT(t.id) tidCount"],
                    ],
                    [
                        'methodName' => 'from',
                        'args' => ["AppBundle:Bin", "b"],
                    ],
                    [
                        'methodName' => 'join',
                        'args' => ["b.travelerIds", "t"],
                    ],
                    [
                        'methodName' => 'join',
                        'args' => ["t.inboundOrder", "i"],
                    ],
                    [
                        'methodName' => 'join',
                        'args' => ["i.client", "c"],
                    ],
                    [
                        'methodName' => 'join',
                        'args' => ["c.organization", "org"],
                    ],
                    [
                        'methodName' => 'groupBy',
                        'args' => ["b.name"],
                    ],
                    [
                        'methodName' => 'addGroupBy',
                        'args' => ["c.name"],
                    ],
                    [
                        'methodName' => 'addGroupBy',
                        'args' => ["i.label"],
                    ],
                    [
                        'methodName' => 'addOrderBy',
                        'args' => ["b.name", "ASC"],
                    ],
                    [
                        'methodName' => 'addOrderBy',
                        'args' => ["c.name", "ASC"],
                    ],
                    [
                        'methodName' => 'addOrderBy',
                        'args' => ["c.name", "ASC"],
                    ],
                ],
                'countParts' => [
                    [
                        'methodName' => 'select',
                        'args' => ["count(DISTINCT CONCAT(b.name, i.label, c.name)) rowCount"],
                    ],
                    [
                        'methodName' => 'from',
                        'args' => ["AppBundle:Bin", "b"],
                    ],
                    [
                        'methodName' => 'join',
                        'args' => ["b.travelerIds", "t"],
                    ],
                    [
                        'methodName' => 'join',
                        'args' => ["t.inboundOrder", "i"],
                    ],
                    [
                        'methodName' => 'join',
                        'args' => ["i.client", "c"],
                    ],
                    [
                        'methodName' => 'join',
                        'args' => ["c.organization", "org"],
                    ],
                ],
                'columns'   => [
                        [
                            'name'  => 'bname',
                            'type'  => 'string',
                            'label' => 'Bin'
                        ],[
                            'name'  => 'cname',
                            'type'  => 'string',
                            'label' => 'Client'
                        ],[
                            'name'  => 'ilabel',
                            'type'  => 'string',
                            'label' => 'Inbound Order'
                        ],[
                            'name'  => 'tidCount',
                            'type'  => 'integer',
                            'label' => 'TravelerId Count'
                        ]
                    ],
                'parameterWhiteList'    => null,
                'singleQueryReportParameters'   => [
                    [
                        'name'  => 'client',
                        'title' => 'Client',
                        'priority'  => 1,
                        'type'  => 'integer',
                        'isFuzzy'   => false,
                        'isHidden'   => false,
                        'isOptional' => true,
                        'template'  => '<label class="label">Client</label><p class="control"<select use_select_2="true" name="client"><option value="">[All]</option></select></p>',
                        'value' => null,
                        'parts' => [
                            [
                                'methodName' => 'andWhere',
                                'args' => ['c.id = :client'],
                            ],
                        ],
                        'choicesPropertyName' => 'clientsChoiceList',
                    ]
                ]
            ],///end
        ];
      }
}