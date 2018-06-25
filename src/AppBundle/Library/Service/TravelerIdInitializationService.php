<?php
namespace AppBundle\Library\Service;

use AppBundle\Entity\TravelerId;
use AppBundle\Entity\Unit;
use AppBundle\Entity\UnitType;
use AppBundle\Entity\User;
use AppBundle\Entity\Organization;
use AppBundle\Entity\AccountAutoPlanChange;

class TravelerIdInitializationService
{
    use \Symfony\Component\DependencyInjection\ContainerAwareTrait;
    use \AppBundle\Controller\Mixin\UpdateAclMixin;

    public function initialize(TravelerId $tid)
    {
        $createdEnities = [];
        $createdEnities[] = $tid->checkUnitStatus();
        return $createdEnities;
    }

    public function checkforPlanAutoUpgrade(User $user)
    {
        $organization = $user->getOrganization();
        $plan = $organization->getAccount()->getSubscription()->getPlan();
        $tidCount = $this->getTravelerIdsInRange(
            $organization,
            $organization->getAccount()->getSubscription()->getCurrentPeriodStart(),
            $organization->getAccount()->getSubscription()->getCurrentPeriodEnd()
        );
        if($tidCount > $plan->getMaxMonthlyTravelerIds()){
            return $this->autoUpgradePlan($user, $tidCount);
        }
        return null;
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

    private function autoUpgradePlan(User $user, $tidCount)
    {
        $organization = $user->getOrganization();
        $newPlan = $this->findNextPlanforUpgrade($tidCount);
        $oldPlan = $organization->getAccount()->getSubscription()->getPlan();

        $planChange = new AccountAutoPlanChange;
        $planChange->setAccount($organization->getAccount());
        $planChange->setChangedBy($user);
        $planChange->setChangedAt(new \DateTime);
        $planChange->setOldPlan($oldPlan);
        $planChange->setNewPlan($newPlan);

        \Stripe\Stripe::setApiKey($this->container->getParameter('stripe_secure_key'));
        $planChange->updateAccount();

        $this->getEntityManager()->persist($planChange);
        $this->getEntityManager()->flush();
        $this->updateAclByRoles($planChange, ['ROLE_USER'=>['view'], 'ROLE_ADMIN'=>'operator']);
    }

    private function findNextPlanforUpgrade($tidCount)
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('p')
            ->from('AppBundle:Plan', 'p')
            ->where('p.maxMonthlyTravelerIds > :tidCount')
            ->andWhere('p.isActive = :active')
            ->orderBy('p.maxMonthlyTravelerIds', 'ASC')
            ->setParameter('tidCount', $tidCount)
            ->setParameter('active', true)
            ->getQuery()->setMaxResults(1)->getOneOrNullResult();

    }

    protected function getEntityManager()
    {
        return $this->container
            ->get('doctrine')
            ->getManager();
    }
}
