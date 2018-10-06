<?php
namespace AppBundle\DataFixtures\ORM\SingleQueryReports;

class ReceivingReport {
 	const REPORT_DATA =  [
        'tag'   => 'client,ion,tid',
        'name'  => 'Receiving Report',
        'description' => 'Client Name, Inbound Order #, TID list with Part/Commodity or Equipment Type within a date range',
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
    ];
}