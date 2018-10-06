<?php
namespace AppBundle\DataFixtures\ORM\SingleQueryReports;

class ShippingReport {
 	const REPORT_DATA = [
        'tag'   => 'customer,oon',
        'name'  => 'Shipping Report',
        'description' => 'Customer Name, Outbound Order #, Shipped At, Manifest Link, Sales Item Count',
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
    ];
}