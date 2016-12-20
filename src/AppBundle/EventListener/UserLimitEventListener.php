<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\User;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;


class UserLimitEventListener
{
    protected $tokenStorage;
    protected $em;

    /** @var Symfony\Component\DependencyInjection\ContainerInterface */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->tokenStorage =  $container->get('security.token_storage');
        $this->em = $container->get('doctrine')->getManager();
    }


    /**
     * On each request we want to check the user limits
     *
     * @param \Symfony\Component\HttpKernel\Event\FilterControllerEvent $event
     * @return void
     */
    public function onCoreController(FilterControllerEvent $event)
    {
        if($this->tokenStorage->getToken()){
            $user = $this->tokenStorage->getToken()->getUser();
            if($user instanceof User){
                if(!$user->getOrganization()->getAccount()->isActive()){
                    throw new \Exception("Account is Not Active");
                }

                $redisClient = $this->container->get('snc_redis.default');
                $orgId = $user->getOrganization()->getId();
                $orgSessionLimit = $user->getOrganization()->getUserLimit();
                $key = 'org#'.$orgId;

                $sessions = $redisClient->hgetall($key);
                $sessionCount = 0;
                $thirtyMinutesAgo = new \DateTime('-30 minute');

                foreach($sessions as $sessionKey => $timeStr){
                  $time = new \DateTime($timeStr);
                  if($time < $thirtyMinutesAgo){
                      $redisClient->hdel($key, $sessionKey);
                  }else{
                      $sessionCount++;
                  }
                }

                if($sessionCount <= $orgSessionLimit){
                  $sessionKey = $this->container->get('session')->getId();
                  $redisClient->hset($key, $sessionKey, (new \DateTime())->format('Y-m-d\TH:i:s.uP') );
                }else{
                  throw new \Exception("Session Limit Reached");
                }
            }
        }
    }
}