<?php

namespace AppBundle\Listener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Debug\Exception\FlattenException;

class ExceptionEventListener
{

    /** @var Symfony\Component\DependencyInjection\ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function handleKernelException(GetResponseForExceptionEvent $event)
    {
        if( !$this->container->get('security.authorization_checker')->isGranted('ROLE_DEV') and
            $this->container->get('kernel')->getEnvironment() !== 'dev'
        ){

            $exception = FlattenException::create($event->getException());

            // First, log the exception to the standard error logs.
            $this->container
                ->get('logger')
                ->error( ' In File '.$exception->getFile().', on line '.$exception->getLine().': '.
                    $exception->getMessage());

            // Determine what the HTTP status code should be.
            if($event->getException() instanceof \Symfony\Component\HttpKernel\Exception\HttpException){
                $httpStatusCode = $event->getException()->getStatusCode();
            }else{
                $httpStatusCode = $exception->getCode();
                if ($exception->getCode() < 100 || $exception->getCode() >= 600) {
                    $httpStatusCode = 500;
                }
            }

            $parameters = [
                'status_code' => $httpStatusCode,
                'status_text' => $exception->getMessage(),
                'exception' => $exception
            ];

            if( in_array('application/json', $event->getRequest()->getAcceptableContentTypes()) ){
                $errorContent = $this->container
                    ->get('templating')
                    ->render(':default:exception.json.twig', $parameters);
            }else{
                $errorContent = $this->container
                    ->get('templating')
                    ->render(':default:error.html.twig', $parameters);
            }

            $response = new Response($errorContent, $httpStatusCode);
            $response->setProtocolVersion('1.1');
            $event->setResponse($response);
        }
    }

}