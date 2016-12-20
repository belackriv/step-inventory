<?php

namespace AppBundle\Security\Firewall;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Firewall\ExceptionListener as BaseExceptionListener;

class ExceptionListener extends BaseExceptionListener
{
    protected function setTargetPath(Request $request)
    {
        /*
        if($request->isXmlHttpRequest()){
            return;
        }
        not sure I need above
        maybe need to check if going to a bad route, like "signup_activation"
		*/

        parent::setTargetPath($request);
    }
}
