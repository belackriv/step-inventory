<?php

namespace AppBundle\Controller;

use AppBundle\Form\SignupType;
use AppBundle\Entity\User;
use AppBundle\Entity\UserRole;
use AppBundle\Entity\Organization;
use AppBundle\Entity\Account;
use AppBundle\Entity\AccountOwnerChange;
use AppBundle\Entity\AccountSubscriptionChange;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends Controller
{
    use Mixin\UpdateAclMixin;

    /**
     * @Route("/login", name="login_route")
     */
    public function loginAction()
    {
        $authenticationUtils = $this->get('security.authentication_utils');

	    // get the login error if there is one
	    $error = $authenticationUtils->getLastAuthenticationError();

	    // last username entered by the user
	    $lastUsername = $authenticationUtils->getLastUsername();

	    return $this->render(
	        'security/login.html.twig',
	        [
	            // last username entered by the user
	            'last_username' => $lastUsername,
	            'error'         => $error,
	        ]
	    );
    }

    /**
     * @Route("/login_check", name="login_check")
     */
    public function loginCheckAction()
    {
        //np-op, caught by firewall
    }

    /**
     * @Route("/signup", name="signup_route")
     */
    public function signupAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(SignupType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();

            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $adminRole =  $this->getDoctrine()->getRepository('AppBundle:Role')->findOneBy([
                'name' => 'Admin'
            ]);
            $adminUserRole = new UserRole;
            $adminUserRole->setRole($adminRole);
            $adminUserRole->setUser($user);
            $em->persist($adminUserRole);

            $user->addUserRole($adminUserRole);
            $em->persist($user);

            $organization = new Organization;
            $organization->setName($form->get('organizationName')->getData());
            $em->persist($organization);
            $user->setOrganization($organization);

            $account = new Account;
            $em->persist($account);
            $accountOwnerChange = new AccountOwnerChange();
            $accountOwnerChange->setChangedBy($user);
            $accountOwnerChange->setChangedAt(new \DateTime);
            $accountOwnerChange->setAccount($account);
            $accountOwnerChange->setNewOwner($user);
            $em->persist($accountOwnerChange);
            $accountOwnerChange->updateAccount();

            $trialSubscription =  $this->getDoctrine()->getRepository('AppBundle:Subscription')->findOneBy([
                'name' => 'Trial'
            ]);
            $accountSubscriptionChange = new AccountSubscriptionChange();
            $accountSubscriptionChange->setChangedBy($user);
            $accountSubscriptionChange->setChangedAt(new \DateTime);
            $accountSubscriptionChange->setAccount($account);
            $accountSubscriptionChange->setNewSubscription($trialSubscription);
            $em->persist($accountSubscriptionChange);
            $accountSubscriptionChange->updateAccount();

            $organization->setAccount($account);

            $em->flush();

            $this->updateAclByRoles($user, ['ROLE_USER'=>'view', 'ROLE_ADMIN'=>'operator']);
            foreach($user->getUserRoles() as $userRole){
                $this->updateAclByRoles($userRole, ['ROLE_USER'=>'view', 'ROLE_ADMIN'=>'operator']);
            }
            $this->updateAclByRoles($organization, ['ROLE_USER'=>'view', 'ROLE_ADMIN'=>'operator']);

            return $this->redirectToRoute('login_route');
        }

        return $this->render(
            'security/signup.html.twig',
            ['form' => $form->createView()]
        );
    }

}