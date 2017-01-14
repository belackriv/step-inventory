<?php

namespace AppBundle\Listener;

use AppBundle\Entity\User;
use AppBundle\Library\Service\SncRedisSessionQueryService;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Debug\Exception\FlattenException;


class UserSessionClosedEventListener
{
	protected $tokenStorage;
	protected $em;

	/** @var Symfony\Component\DependencyInjection\ContainerInterface */
	protected $container;

	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
	}


	/**
	 * On each request we want to check the user limits
	 *
	 * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
	 * @return void
	 */
	public function onKernelRequest(GetResponseEvent $event)
	{
		$redisClient = $this->container->get('snc_redis.default');
		$this->container->get('session')->start();
		$sessionKey = $this->container->get('session')->getId();
		$sessionQueryService = new SncRedisSessionQueryService($redisClient);
		if($sessionQueryService->isSessionInvalid($sessionKey)){
			if( $event->getRequest()->isXmlHttpRequest()){
			  	$errorContent = $this->container
                    ->get('templating')
                    ->render(':default:exception.json.twig', [
                    	'status_code' => 403,
						'status_text' => 'Session is Closed',
						'exception' => FlattenException::create(new \Exception('Session is Closed.'))
                ]);
			}else{
				$errorContent = $this->container
	                ->get('templating')
	                ->render(':security:session-closed.html.twig', []);

	        }
			$response = new Response($errorContent, 403);
			$cookieParams = session_get_cookie_params();
			$cookie = new Cookie(
				$this->container->get('session')->getName(), '', time() - 42000,
				$cookieParams["path"], $cookieParams["domain"], $cookieParams["secure"], $cookieParams["httponly"]
			);
			$response->headers->setCookie($cookie);
            $response->setProtocolVersion('1.1');
            $redisClient->del(SncRedisSessionQueryService::SESSION_PREFIX.$sessionKey.SncRedisSessionQueryService::INVALID_SESSION_SUFFIX);
            $event->setResponse($response);
		}
	}
}