<?php
namespace AppBundle\DataFixtures\ORM\SingleQueryReports;

class BinReport {
 	const REPORT_DATA =  [
        'tag'   => 'office,department,bin,tid',
        'name'  => 'Bin Report',
        'description' => 'Office, Department, and Bin TID list with Part/Commodity or Equipment Type within a date range',
        'filename'  => 'bin_report',
        'roles' => ['ROLE_USER'],
        'parts' => [
            [
                'methodName' => 'select',
                'args' => ["o.name oName", "d.name dName","b.name bName","t.label tLabel","sku.label skuLabel",
                    "p.name pName","com.name comName","ut.name utName"],
            ],[
                'methodName' => 'from',
                'args' => ["AppBundle:TravelerId", "t"],
            ],[
                'methodName' => 'join',
                'args' => ["t.bin", "b"],
            ],[
                'methodName' => 'join',
                'args' => ["b.department", "d"],
            ],[
                'methodName' => 'join',
                'args' => ["d.office", "o"],
            ],[
                'methodName' => 'join',
                'args' => ["o.organization", "org"],
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
                'methodName' => 'join',
                'args' => ["t.bin", "b"],
            ],[
                'methodName' => 'join',
                'args' => ["b.department", "d"],
            ],[
                'methodName' => 'join',
                'args' => ["d.office", "o"],
            ],[
                'methodName' => 'join',
                'args' => ["o.organization", "org"],
            ],
        ],
        'columns'   => [
                [
                    'name'  => 'oName',
                    'type'  => 'string',
                    'label' => 'Office'
                ],[
                    'name'  => 'dName',
                    'type'  => 'string',
                    'label' => 'Department'
                ],[
                    'name'  => 'bName',
                    'type'  => 'string',
                    'label' => 'Bin'
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
                'name'  => 'bin',
                'title' => 'Bin',
                'priority'  => 1,
                'type'  => 'integer',
                'isFuzzy'   => false,
                'isHidden'   => false,
                'isOptional' => true,
                'template'  => '<label class="label">Bin</label><div class="control"><select style="width:100%" use_select_2="true" name="bin"><option value="">[All]</option></select></div>',
                'value' => null,
                'parts' => [
                    [
                        'methodName' => 'andWhere',
                        'args' => ['t.bin = :bin'],
                    ],
                ],
                'choicesPropertyName' => 'binsChoiceList',
            ]
        ]
    ];
}