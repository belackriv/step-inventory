<?php
namespace AppBundle\Library\Stripe;

use AppBundle\Entity\Bill;

class InvoiceCollector
{
    use \Symfony\Component\DependencyInjection\ContainerAwareTrait;

    public function collect()
    {
        $em = $this->container->get('doctrine')->getManager();

        $localBills = $this->container->get('doctrine')->getRepository('AppBundle:Bill')->findAll();

        \Stripe\Stripe::setApiKey($this->container->getParameter('stripe_secure_key'));
        $stripeInvoices = \Stripe\Invoice::all();
        $stripeInvoiceCount = 0;
        foreach($stripeInvoices->autoPagingIterator() as $stripInvoice){
            $stripeInvoiceCount++;
            $localBill = $this->container->get('doctrine')->getRepository('AppBundle:Bill')->findOneBy(['externalId' => $stripInvoice->id]);
            if(!$localBill){
                $localBill = new Bill();
                $em->persist($localBill);
            }
            $localBill->updateFromStripe($stripInvoice);
            $localAccount = $this->container->get('doctrine')->getRepository('AppBundle:Account')->findOneBy(['externalId' => $stripInvoice->customer]);
            if(!$localAccount){
                $em->remove($localBill);
            }else{
                $localBill->setAccount($localAccount);
            }
        }

        $em->flush();
        return $stripeInvoiceCount;
    }

}