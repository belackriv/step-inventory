<?php

namespace AppBundle\Listener;

use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Debug\Exception\FlattenException;

class AjaxAuthenticationEventListener
{
   /** @var Symfony\Component\DependencyInjection\ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Handles security related exceptions.
     *
     * @param GetResponseForExceptionEvent $event An GetResponseForExceptionEvent instance
     */
    public function onCoreException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $request = $event->getRequest();

        if ($request->isXmlHttpRequest()) {
            $httpStatusCode = null;
            $httpStatusText = null;
            if($exception instanceof AuthenticationException || $exception instanceof AuthenticationCredentialsNotFoundException){
                $httpStatusCode = 401;
                $httpStatusText = 'User Not Authenticated';
            }else if($this->container->get('security.token_storage')->getToken()->getUser() === 'anon.'){
                $httpStatusCode = 401;
                $httpStatusText = 'User Is Anonymous';
            }else if($exception instanceof AccessDeniedException){
                $httpStatusCode = 403;
                $httpStatusText = 'Access Denied';
            }

            if($httpStatusCode and $httpStatusText){
                $flatException = FlattenException::create($exception);

                $parameters = [
                    'status_code' => $httpStatusCode,
                    'status_text' => $httpStatusText,
                    'exception' => $flatException
                ];

                $errorContent = $this->container
                        ->get('templating')
                        ->render(':default:exception.json.twig', $parameters);
                $response = new Response($errorContent, $httpStatusCode);
                $response->setProtocolVersion('1.1');
                $event->setResponse($response);
            }
        }
    }
}