<?php
namespace AppBundle\DataFixtures\ORM\SingleQueryReports;

class InboundOrdersReport {
 	const REPORT_DATA = [
        'tag'   => 'client,ion',
        'name'  => 'Inbound Orders Report',
        'description' => 'Client Name, Inbound Order #, Expected At, Received At, Manifest Link, Description',
        'filename'  => 'inbound_orders_report',
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
    ];
}