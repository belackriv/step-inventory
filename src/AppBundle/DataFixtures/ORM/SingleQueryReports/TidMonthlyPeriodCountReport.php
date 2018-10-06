<?php
namespace AppBundle\DataFixtures\ORM\SingleQueryReports;

class TidMonthlyPeriodCountReport {
 	const REPORT_DATA = [
        'tag'   => 'tid',
        'name'  => 'TID Count Current Month to Date',
        'description' => 'Just the current monthly period tid count',
        'filename'  => 'tid_monthly_period_count',
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
    ];
}