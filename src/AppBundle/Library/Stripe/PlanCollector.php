<?php
namespace AppBundle\Library\Stripe;

use AppBundle\Entity\Plan;

class PlanCollector
{
    use \Symfony\Component\DependencyInjection\ContainerAwareTrait;

    public function collect()
    {
        $em = $this->container->get('doctrine')->getManager();

        $localPlans = $this->container->get('doctrine')->getRepository('AppBundle:Plan')->findAll();

        \Stripe\Stripe::setApiKey($this->container->getParameter('stripe_secure_key'));
        $stripePlans = \Stripe\Plan::all();
        $stripePlanCount = 0;
        foreach($stripePlans->autoPagingIterator() as $stripPlan){
            $stripePlanCount++;
            $localPlan = $this->container->get('doctrine')->getRepository('AppBundle:Plan')->findOneBy(['externalId' => $stripPlan->id]);
            if(!$localPlan){
                $localPlan = new Plan();
                $em->persist($localPlan);
            }
            $localPlan->updateFromStripe($stripPlan);
            $localPlan->setIsActive(true);
            $localPlanIndex = array_search($localPlan, $localPlans, true);
            if($localPlanIndex !== false){
                unset($localPlans[$localPlanIndex]);
            }
        }

        foreach($localPlans as $localPlan){
            $localPlan->setIsActive(false);
        }

        $em->flush();
        return $stripePlanCount;
    }

}