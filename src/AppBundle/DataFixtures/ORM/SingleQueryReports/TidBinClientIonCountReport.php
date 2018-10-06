<?php
namespace AppBundle\DataFixtures\ORM\SingleQueryReports;

class TidBinClientIonCountReport {
 	const REPORT_DATA = [
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
	];
}