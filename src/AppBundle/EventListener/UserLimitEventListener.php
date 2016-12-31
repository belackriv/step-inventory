<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\User;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
					if( $event->getRequest()->isXmlHttpRequest()){
						if(!$this->isAccountRelatedUrl($event->getRequest())){
							throw new \Exception("Account is Not Active");
						}
					}else{
						if(preg_match('/profile/', $event->getRequest()->getPathInfo()) !== 1){
							$event->setController(function(){
						        return new RedirectResponse('/profile');
						    });
						}
					}
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
					if( $event->getRequest()->isXmlHttpRequest()){
					  	throw new \Exception("Active Session Limit Reached");
					}else{
						$errorContent = $this->container
		                    ->get('templating')
		                    ->render(':security:session-limit.html.twig', []);
						$response = new Response($errorContent, 403);
			            $response->setProtocolVersion('1.1');
			            $event->setController(function() use ($response){
					        return $response;
					    });
					}
				}
			}
		}
	}

	private function isAccountRelatedUrl(Request $request)
	{
		if(
			preg_match('/myself/', $request->getPathInfo()) === 1 or
			preg_match('/office/', $request->getPathInfo()) === 1 or
			preg_match('/account/', $request->getPathInfo()) === 1 or
			preg_match('/profile/', $request->getPathInfo()) === 1 or
			preg_match('/account_change/', $request->getPathInfo()) === 1 or
			preg_match('/subscription/', $request->getPathInfo()) === 1 or
			preg_match('/subscription_cancel/', $request->getPathInfo()) === 1 or
			preg_match('/payment_source/', $request->getPathInfo()) === 1 or
			preg_match('/plan/', $request->getPathInfo()) === 1 or
			preg_match('/user/', $request->getPathInfo()) === 1 or
			preg_match('/menu_link/', $request->getPathInfo()) === 1
		){
			return true;
		}else{
			return false;
		}
	}
}