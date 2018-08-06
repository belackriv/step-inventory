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
        return [
            //#1
            [
                'tag'   => 'bin,client,ion,tid',
                'name'  => 'TID Count By Bin, Client, and ION',
                'description' => 'A basic tid count by bin, client, and inbound order.',
                'filename'  => 'tid_bin_client_ion_count',
                'roles' => ['ROLE_USER'],
                'parts' => [
                    [
                        'methodName' => 'select',
                        'args' => ["b.name bname", "c.name cname", "i.label ilabel", "COUNT(t.id) tidCount"],
                    ],[
                        'methodName' => 'from',
                        'args' => ["AppBundle:Bin", "b"],
                    ],[
                        'methodName' => 'join',
                        'args' => ["b.travelerIds", "t"],
                    ],[
                        'methodName' => 'join',
                        'args' => ["t.inboundOrder", "i"],
                    ],[
                        'methodName' => 'join',
                        'args' => ["i.client", "c"],
                    ],[
                        'methodName' => 'join',
                        'args' => ["c.organization", "org"],
                    ],[
                        'methodName' => 'groupBy',
                        'args' => ["b.name"],
                    ],[
                        'methodName' => 'addGroupBy',
                        'args' => ["c.name"],
                    ],[
                        'methodName' => 'addGroupBy',
                        'args' => ["i.label"],
                    ],[
                        'methodName' => 'addOrderBy',
                        'args' => ["b.name", "ASC"],
                    ],[
                        'methodName' => 'addOrderBy',
                        'args' => ["c.name", "ASC"],
                    ],[
                        'methodName' => 'addOrderBy',
                        'args' => ["c.name", "ASC"],
                    ],
                ],
                'countParts' => [
                    [
                        'methodName' => 'select',
                        'args' => ["count(DISTINCT CONCAT(b.name, i.label, c.name)) rowCount"],
                    ],[
                        'methodName' => 'from',
                        'args' => ["AppBundle:Bin", "b"],
                    ],[
                        'methodName' => 'join',
                        'args' => ["b.travelerIds", "t"],
                    ],[
                        'methodName' => 'join',
                        'args' => ["t.inboundOrder", "i"],
                    ],[
                        'methodName' => 'join',
                        'args' => ["i.client", "c"],
                    ],[
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
                        'template'  => '<label class="label">Client</label><div class="control"><select style="width:100%" use_select_2="true" name="client"><option value="">[All]</option></select></div>',
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
            ],//#1 end
            //#2
            [
                'tag'   => 'tid',
                'name'  => 'TID Count Current Month to Date',
                'description' => 'Just the current monthly period tid count',
                'filename'  => 'tid_bin_client_ion_count',
                'roles' => ['ROLE_USER'],
                'parts' => [
                    [
                        'methodName' => 'select',
                        'args' => ["MIN(sub.currentPeriodStart) pStart", "MAX(sub.currentPeriodEnd) pEnd","COUNT(t.id) tidCount"],
                    ],[
                        'methodName' => 'from',
                        'args' => ["AppBundle:TravelerId", "t"],
                    ],[
                        'methodName' => 'join',
                        'args' => ["t.inboundOrder", "i"],
                    ],[
                        'methodName' => 'join',
                        'args' => ["i.client", "c"],
                    ],[
                        'methodName' => 'join',
                        'args' => ["c.organization", "org"],
                    ],[
                        'methodName' => 'join',
                        'args' => ["org.account", "acc"],
                    ],[
                        'methodName' => 'join',
                        'args' => ["acc.subscription", "sub"],
                    ],[
                        'methodName' => 'andWhere',
                        'args' => ['t.createdAt >= sub.currentPeriodStart'],
                    ],[
                        'methodName' => 'andWhere',
                        'args' => ['t.createdAt <= sub.currentPeriodEnd'],
                    ],
                    /*
                    [
                        'methodName' => 'andWhere',
                        'args' => ['t.createdAt >= DATE_FORMAT(CURRENT_TIMESTAMP() ,\'%Y-%m-01\')'],
                    ],
                    */
                ],
                'countParts' => [
                    [
                        'methodName' => 'select',
                        'args' => ["1 as rowCount"],
                    ],[
                        'methodName' => 'from',
                        'args' => ["AppBundle:Organization", "org"],
                    ],
                ],
                'columns'   => [
                    [
                        'name'  => 'pStart',
                        'type'  => 'datetime',
                        'label' => 'Period Start'
                    ],[
                        'name'  => 'pEnd',
                        'type'  => 'datetime',
                        'label' => 'Period End'
                    ],[
                        'name'  => 'tidCount',
                        'type'  => 'integer',
                        'label' => 'TravelerId Count'
                    ]
                ],
                'parameterWhiteList'    => null,
                'singleQueryReportParameters'   => []
            ],//#2 end
            //#3
            [
                'tag'   => 'client,ion,tid',
                'name'  => 'Receiving Report',
                'description' => 'Customer Name, Inbound Order #, TID list with Part/Commodity or Equipment Type within a date range',
                'filename'  => 'receiving_report',
                'roles' => ['ROLE_USER'],
                'parts' => [
                    [
                        'methodName' => 'select',
                        'args' => ["c.name cname", "i.label ilabel","i.receivedAt receivedAt","t.label tLabel","sku.label skuLabel",
                            "p.name pName","com.name comName","ut.name utName"],
                    ],[
                        'methodName' => 'from',
                        'args' => ["AppBundle:TravelerId", "t"],
                    ],[
                        'methodName' => 'join',
                        'args' => ["t.inboundOrder", "i"],
                    ],[
                        'methodName' => 'join',
                        'args' => ["i.client", "c"],
                    ],[
                        'methodName' => 'join',
                        'args' => ["c.organization", "org"],
                    ],[
                        'methodName' => 'join',
                        'args' => ["t.sku", "sku"],
                    ],[
                        'methodName' => 'leftJoin',
                        'args' => ["sku.part", "p"],
                    ],[
                        'methodName' => 'leftJoin',
                        'args' => ["sku.commodity", "com"],
                    ],[
                        'methodName' => 'leftJoin',
                        'args' => ["sku.unitType", "ut"],
                    ],[
                        'methodName' => 'addOrderBy',
                        'args' => ["t.label", "ASC"],
                    ],
                ],
                'countParts' => [
                    [
                        'methodName' => 'select',
                        'args' => ["count(t.id) rowCount"],
                    ],[
                        'methodName' => 'from',
                        'args' => ["AppBundle:TravelerId", "t"],
                    ],[
                        'methodName' => 'join',
                        'args' => ["t.inboundOrder", "i"],
                    ],[
                        'methodName' => 'join',
                        'args' => ["i.client", "c"],
                    ],[
                        'methodName' => 'join',
                        'args' => ["c.organization", "org"],
                    ],
                ],
                'columns'   => [
                        [
                            'name'  => 'cname',
                            'type'  => 'string',
                            'label' => 'Client'
                        ],[
                            'name'  => 'ilabel',
                            'type'  => 'string',
                            'label' => 'Inbound Order'
                        ],[
                            'name'  => 'receivedAt',
                            'type'  => 'datetime',
                            'label' => 'Received At'
                        ],[
                            'name'  => 'tLabel',
                            'type'  => 'string',
                            'label' => 'TravelerId Label'
                        ],[
                            'name'  => 'skuLabel',
                            'type'  => 'string',
                            'label' => 'SKU Label'
                        ],[
                            'name'  => 'pName',
                            'type'  => 'string',
                            'label' => 'Part Name'
                        ],[
                            'name'  => 'comName',
                            'type'  => 'string',
                            'label' => 'Commodity Name'
                        ],[
                            'name'  => 'utName',
                            'type'  => 'string',
                            'label' => 'Unit Type Name'
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
                        'template'  => '<label class="label">Client</label><div class="control"><select style="width:100%" use_select_2="true" name="client"><option value="">[All]</option></select></div>',
                        'value' => null,
                        'parts' => [
                            [
                                'methodName' => 'andWhere',
                                'args' => ['c.id = :client'],
                            ],
                        ],
                        'choicesPropertyName' => 'clientsChoiceList',
                    ],[
                        'name'  => 'received_after_date',
                        'title' => 'Received After Date',
                        'priority'  => 2,
                        'type'  => 'datetime',
                        'isFuzzy'   => false,
                        'isHidden'   => false,
                        'isOptional' => true,
                        'template'  => '<label class="label">Received After Date</label><div class="control"><input name="received_after_date" type="date" /></div>',
                        'value' => null,
                        'parts' => [
                             [
                                'methodName' => 'andWhere',
                                'args' => ["i.receivedAt >= :received_after_date"],
                            ],
                        ],
                        'choicesPropertyName' => null,
                    ],[
                        'name'  => 'received_before_date',
                        'title' => 'Received Before Date',
                        'priority'  => 3,
                        'type'  => 'datetime',
                        'isFuzzy'   => false,
                        'isHidden'   => false,
                        'isOptional' => true,
                        'template'  => '<label class="label">Received Before Date</label><div class="control"><input name="received_before_date" type="date" /></div>',
                        'value' => null,
                        'parts' => [
                            [
                                'methodName' => 'andWhere',
                                'args' => ["i.receivedAt <= :received_before_date"],
                            ],
                        ],
                        'choicesPropertyName' => null,
                    ],
                ]
            ],//#3 end
            //#4
            [
                'tag'   => 'customer,oon',
                'name'  => 'Shipping Report',
                'description' => 'Customer Name, Inbound Order #, TID list with Part/Commodity or Equipment Type within a date range',
                'filename'  => 'shipping_report',
                'roles' => ['ROLE_USER'],
                'parts' => [
                    [
                        'methodName' => 'select',
                        'args' => ["c.name cName", "o.label olabel","o.shippedAt shippedAt","o.id oId","COUNT(s.id) sCount"],
                    ],[
                        'methodName' => 'from',
                        'args' => ["AppBundle:SalesItem", "s"],
                    ],[
                        'methodName' => 'join',
                        'args' => ["s.outboundOrder", "o"],
                    ],[
                        'methodName' => 'join',
                        'args' => ["o.customer", "c"],
                    ],[
                        'methodName' => 'join',
                        'args' => ["c.organization", "org"],
                    ],[
                        'methodName' => 'groupBy',
                        'args' => ["c.name"],
                    ],[
                        'methodName' => 'addGroupBy',
                        'args' => ["o.label"],
                    ],[
                        'methodName' => 'addGroupBy',
                        'args' => ["o.shippedAt"],
                    ],[
                        'methodName' => 'addGroupBy',
                        'args' => ["o.id"],
                    ],[
                        'methodName' => 'addOrderBy',
                        'args' => ["o.shippedAt", "ASC"],
                    ],
                ],
                'countParts' => [
                    [
                        'methodName' => 'select',
                        'args' => ["count(o.id) rowCount"],
                    ],[
                        'methodName' => 'from',
                        'args' => ["AppBundle:SalesItem", "s"],
                    ],[
                        'methodName' => 'join',
                        'args' => ["s.outboundOrder", "o"],
                    ],[
                        'methodName' => 'join',
                        'args' => ["o.customer", "c"],
                    ],[
                        'methodName' => 'join',
                        'args' => ["c.organization", "org"],
                    ]
                ],
                'columns'   => [
                        [
                            'name'  => 'cName',
                            'type'  => 'string',
                            'label' => 'Client'
                        ],[
                            'name'  => 'olabel',
                            'type'  => 'string',
                            'label' => 'Outbound Order'
                        ],[
                            'name'  => 'shippedAt',
                            'type'  => 'datetime',
                            'label' => 'Shipped At'
                        ],[
                            'name'  => 'oId',
                            'type'  => 'integer',
                            'helper' => 'sqrTemplate',
                            'helperOptions' => [
                                'template' => '<a data-ui-action="showOutboundManifest" data-id="{{value}}" href="/outbound_order/{{value}}/manifest">Show Manifest</a>'
                            ],
                            'label' => 'Manifest Link'
                        ],[
                            'name'  => 'sCount',
                            'type'  => 'integer',
                            'label' => 'Sales Item Count'
                        ]
                    ],
                'parameterWhiteList'    => null,
                'singleQueryReportParameters'   => [
                    [
                        'name'  => 'customer',
                        'title' => 'Customer',
                        'priority'  => 1,
                        'type'  => 'integer',
                        'isFuzzy'   => false,
                        'isHidden'   => false,
                        'isOptional' => true,
                        'template'  => '<label class="label">Customer</label><div class="control"><select style="width:100%" use_select_2="true" name="customer"><option value="">[All]</option></select></div>',
                        'value' => null,
                        'parts' => [
                            [
                                'methodName' => 'andWhere',
                                'args' => ['c.id = :customer'],
                            ],
                        ],
                        'choicesPropertyName' => 'customersChoiceList',
                    ],[
                        'name'  => 'shipped_after_date',
                        'title' => 'Shipped After Date',
                        'priority'  => 2,
                        'type'  => 'datetime',
                        'isFuzzy'   => false,
                        'isHidden'   => false,
                        'isOptional' => true,
                        'template'  => '<label class="label">Shipped After Date</label><div class="control"><input name="shipped_after_date" type="date" /></div>',
                        'value' => null,
                        'parts' => [
                             [
                                'methodName' => 'andWhere',
                                'args' => ["i.receivedAt >= :shipped_after_date"],
                            ],
                        ],
                        'choicesPropertyName' => null,
                    ],[
                        'name'  => 'shipped_before_date',
                        'title' => 'Shipped Before Date',
                        'priority'  => 3,
                        'type'  => 'datetime',
                        'isFuzzy'   => false,
                        'isHidden'   => false,
                        'isOptional' => true,
                        'template'  => '<label class="label">Shipped Before Date</label><div class="control"><input name="shipped_before_date" type="date" /></div>',
                        'value' => null,
                        'parts' => [
                            [
                                'methodName' => 'andWhere',
                                'args' => ["i.shippedAt <= :shipped_before_date"],
                            ],
                        ],
                        'choicesPropertyName' => null,
                    ],
                ]
            ],//#4 end


            //#5
            [
                'tag'   => 'client,ion',
                'name'  => 'Inbound Orders Report',
                'description' => 'Customer Name, Inbound Order #, TID list with Part/Commodity or Equipment Type within a date range',
                'filename'  => 'receiving_report',
                'roles' => ['ROLE_USER'],
                'parts' => [
                    [
                        'methodName' => 'select',
                        'args' => ["c.name cName", "i.label ilabel","i.expectedAt expectedAt", "i.receivedAt receivedAt", "i.id iId", 'i.description iDescription'],
                    ],[
                        'methodName' => 'from',
                        'args' => ["AppBundle:InboundOrder", "i"],
                    ],[
                        'methodName' => 'join',
                        'args' => ["i.client", "c"],
                    ],[
                        'methodName' => 'join',
                        'args' => ["c.organization", "org"],
                    ],[
                        'methodName' => 'addOrderBy',
                        'args' => ["i.expectedAt", "ASC"],
                    ],
                ],
                'countParts' => [
                    [
                        'methodName' => 'select',
                        'args' => ["count(i.id) rowCount"],
                    ],[
                        'methodName' => 'from',
                        'args' => ["AppBundle:InboundOrder", "i"],
                    ],[
                        'methodName' => 'join',
                        'args' => ["i.client", "c"],
                    ],[
                        'methodName' => 'join',
                        'args' => ["c.organization", "org"],
                    ],
                ],
                'columns'   => [
                        [
                            'name'  => 'cName',
                            'type'  => 'string',
                            'label' => 'Client'
                        ],[
                            'name'  => 'ilabel',
                            'type'  => 'string',
                            'label' => 'Inbound Order'
                        ],[
                            'name'  => 'expectedAt',
                            'type'  => 'datetime',
                            'label' => 'Expected At'
                        ],[
                            'name'  => 'receivedAt',
                            'type'  => 'datetime',
                            'label' => 'Received At'
                        ],[
                            'name'  => 'iId',
                            'type'  => 'integer',
                            'helper' => 'sqrTemplate',
                            'helperOptions' => [
                                'template' => '<a data-ui-action="showInboundManifest" data-id="{{value}}" href="/inbound_order/{{value}}/manifest">Show Manifest</a>'
                            ],
                            'label' => 'Manifest Link'
                        ],[
                            'name'  => 'iDescription',
                            'type'  => 'string',
                            'label' => 'Description'
                        ],
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
                        'template'  => '<label class="label">Client</label><div class="control"><select style="width:100%" use_select_2="true" name="client"><option value="">[All]</option></select></div>',
                        'value' => null,
                        'parts' => [
                            [
                                'methodName' => 'andWhere',
                                'args' => ['c.id = :client'],
                            ],
                        ],
                        'choicesPropertyName' => 'clientsChoiceList',
                    ],[
                        'name'  => 'order_status',
                        'title' => 'Order Status',
                        'priority'  => 2,
                        'type'  => 'boolean',
                        'isFuzzy'   => false,
                        'isHidden'   => false,
                        'isOptional' => true,
                        'template'  => '<label class="label">Order Status</label><div class="control"><select style="width:100%" name="order_status"><option value="">[All]</option><option value="true"> Received</option><option value="false">Not Received</option></select></div>',
                        'value' => null,
                        'parts' => [
                            [
                                'methodName' => 'andWhere',
                                'args' => ['i.isReceived = :order_status'],
                            ],
                        ],
                        'choicesPropertyName' => null,
                    ],[
                        'name'  => 'expected_after_date',
                        'title' => 'Expected After Date',
                        'priority'  => 3,
                        'type'  => 'datetime',
                        'isFuzzy'   => false,
                        'isHidden'   => false,
                        'isOptional' => true,
                        'template'  => '<label class="label">Expected After Date</label><div class="control"><input name="expected_after_date" type="date" /></div>',
                        'value' => null,
                        'parts' => [
                             [
                                'methodName' => 'andWhere',
                                'args' => ["i.expectedAt >= :expected_after_date"],
                            ],
                        ],
                        'choicesPropertyName' => null,
                    ],[
                        'name'  => 'expected_before_date',
                        'title' => 'Expected Before Date',
                        'priority'  => 4,
                        'type'  => 'datetime',
                        'isFuzzy'   => false,
                        'isHidden'   => false,
                        'isOptional' => true,
                        'template'  => '<label class="label">Expected Before Date</label><div class="control"><input name="expected_before_date" type="date" /></div>',
                        'value' => null,
                        'parts' => [
                            [
                                'methodName' => 'andWhere',
                                'args' => ["i.expectedAt <= :received_before_date"],
                            ],
                        ],
                        'choicesPropertyName' => null,
                    ],
                ]
            ],//#5 end


        ];
      }
}