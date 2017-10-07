<?php
namespace AppBundle\Library\Stripe;

use AppBundle\Entity\Subscription;

class SubscriptionCollector
{
    use \Symfony\Component\DependencyInjection\ContainerAwareTrait;

    public function collect()
    {
        $em = $this->container->get('doctrine')->getManager();

        $localSubscriptions = $this->container->get('doctrine')->getRepository('AppBundle:Subscription')->findAll();

        \Stripe\Stripe::setApiKey($this->container->getParameter('stripe_secure_key'));
        $stripeSubscriptions = \Stripe\Subscription::all();
        $stripeSubscriptionCount = 0;
        foreach($stripeSubscriptions->autoPagingIterator() as $stripSubscription){
            $stripeSubscriptionCount++;
            $localSubscription = $this->container->get('doctrine')->getRepository('AppBundle:Subscription')->findOneBy(['externalId' => $stripSubscription->id]);
            if(!$localSubscription){
                $localSubscription = new Subscription();
                $em->persist($localSubscription);
            }
            $localSubscription->updateFromStripe($stripSubscription);

            $localPlan = $this->container->get('doctrine')->getRepository('AppBundle:Plan')->findOneBy(['externalId' => $stripSubscription->plan->id]);
            $localSubscription->setPlan($localPlan);
            $localAccount = $this->container->get('doctrine')->getRepository('AppBundle:Account')->findOneBy(['externalId' => $stripSubscription->customer]);
            $localSubscription->setAccount($localAccount);
            $localAccount->setSubscription($localSubscription);
            $localSubscriptionIndex = array_search($localSubscription, $localSubscriptions, true);
            if($localSubscriptionIndex !== false){
                unset($localSubscriptions[$localSubscriptionIndex]);
            }
        }

        foreach($localSubscriptions as $localSubscription){
            $localSubscription->setStatus(Subscription::STATUS_INACTIVE);
        }

        $em->flush();
        return $stripeSubscriptionCount;
    }

}