<?php

namespace AppBundle\Library\Service;

use AppBundle\Entity\Organization;

class MonthlyTravelerIdLimitService extends AbstractService
{
    const OVERAGE_LINE_ITEM_TITLE = 'TravelerId Overage Charge';

    public function processMonthlyTravelerIdOverageForStripe(Organization $organization, array $invoiceData)
    {
        $start = new \DateTime('@'.$invoiceData['period_start']);
        $end = new \DateTime('@'.$invoiceData['period_end']);
        $tidCount = $this->getTravelerIdsInRange($organization, $start, $end);
        $tidOverageCount = $tidCount - $organization->getAccount()->getSubscription()->getPlan()->getMaxMonthlyTravelerIds();
        if(
            $tidCount > 0 and
            $this->getOverageLineItem($invoiceData) === null
        ){
            $this->createStripeInvoiceItem($organization, $invoiceData, $tidOverageCount);
        }

    }

    public function getOverageLineItem(array $invoiceData)
    {
        foreach($invoiceData['lines']['data'] as $lineItem){
            if($lineItem['description'] === self::OVERAGE_LINE_ITEM_TITLE){
                return $lineItem;
            }
        }
        return null;
    }

    public function createStripeInvoiceItem(Organization $organization, $invoiceData, $tidOverageCount)
    {
        $amount = $tidOverageCount * $organization->getAccount()->getSubscription()->getPlan()->getTravelerIdOverageCharge();
        $response = \Stripe\InvoiceItem::create(array(
            'customer' => $organization->getAccount()->getExternalId(),
            'invoice' => $invoiceData['id'],
            'amount' => $amount,
            'currency' => 'usd',
            'description' => self::OVERAGE_LINE_ITEM_TITLE)
        );
    }

	public function getTravelerIdsInRange(Organization $organization, \DateTime $start, \DateTime $end)
    {
    	return $this->getEntityManager()->createQueryBuilder()
            ->select('COUNT(tid.id)')
            ->from('AppBundle:TravelerId', 'tid')
            ->join('tid.inboundOrder', 'o')
            ->join('o.client', 'c')
            ->where('c.organization = :org')
            ->andWhere('tid.createdAt >= :start')
            ->andWhere('tid.createdAt <= :end')
            ->setParameter('org', $organization)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()->getSingleScalarResult();
    }

    public function getOrganizationFromInvoicData(array $invoiceData)
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('org')
            ->from('AppBundle:Organization', 'org')
            ->join('org.account', 'a')
            ->where('a.externalId = :customer_id')
            ->setParameter('customer_id', $invoiceData['customer'])
            ->getQuery()->getOneOrNullResult();
    }
}