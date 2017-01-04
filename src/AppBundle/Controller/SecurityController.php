<?php

namespace AppBundle\Controller;

use AppBundle\Form\SignupType;
use AppBundle\Form\ResetPasswordType;
use AppBundle\Form\ResetPasswordUpdateType;
use AppBundle\Entity\User;
use AppBundle\Entity\UserRole;
use AppBundle\Entity\Organization;
use AppBundle\Entity\Account;
use AppBundle\Entity\Subscription;
use AppBundle\Entity\AccountOwnerChange;
use AppBundle\Entity\AccountSubscriptionChange;
use AppBundle\Entity\Office;
use AppBundle\Entity\Department;
use AppBundle\Library\Service\TokenGeneratorService;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
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
            $user->activationToken = TokenGeneratorService::generateToken();
            $user->setIsActive(false);
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

            $plan = $form->get('plan')->getData();

            \Stripe\Stripe::setApiKey($this->container->getParameter('stripe_secure_key'));
            $stripeCustomer = \Stripe\Customer::create([
              "email" => $user->getEmail(),
              "plan" => $plan->getExternalId(),
              "quantity" => 1
            ]);
            $account->setExternalId($stripeCustomer->id);
            $stripeSubscription = $stripeCustomer->subscriptions->data[0];

            $subscription = new Subscription();
            $subscription->setAccount($account);
            $subscription->setPlan($plan);
            $subscription->updateFromStripe($stripeSubscription);
            $em->persist($subscription);

            $organization->setAccount($account);
            $account->setSubscription($subscription);

            $office = new Office();
            $office->setName('Main Office');
            $office->setOrganization($organization);
            $em->persist($office);

            $department = new Department();
            $department->setName('Default Department');
            $department->setOffice($office);
            $em->persist($department);

            $em->flush();

            $this->updateAclByRoles($user, ['ROLE_USER'=>'view', 'ROLE_ADMIN'=>'operator']);
            foreach($user->getUserRoles() as $userRole){
                $this->updateAclByRoles($userRole, ['ROLE_USER'=>'view', 'ROLE_ADMIN'=>'operator']);
            }
            $this->updateAclByRoles($organization, ['ROLE_USER'=>'view', 'ROLE_ADMIN'=>'operator']);
            $this->updateAclByRoles($office, ['ROLE_USER'=>'view', 'ROLE_ADMIN'=>'operator']);
            $this->updateAclByRoles($department, ['ROLE_USER'=>'view', 'ROLE_ADMIN'=>'operator']);

            $body = $this->get('templating')
                ->render('email/signup-complete.html.twig', ['user' => $user, 'token' => $user->activationToken]);

            $message = \Swift_Message::newInstance()
                ->setTo($user->getEmail())
                ->setFrom($this->getParameter('from_email'))
                ->setSubject('Step Inventory Account Activation')
                ->setContentType('text/html')
                ->setBody($body);

            $this->get('mailer')->send($message);

            return $this->render('security/signup-complete.html.twig',['user' => $user]);
        }

        return $this->render(
            'security/signup.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * @Route("/signup_activation", name="signup_activation")
     */
    public function signupActivationAction(Request $request)
    {
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy([
            'activationToken' => $request->query->get('token')
        ]);
        if(!$user){
            throw $this->createNotFoundException('Activation Token '.$request->query->get('token').' Not Found');
        }
        $user->activationToken = null;
        $user->setIsActive(true);
        $this->getDoctrine()->getManager()->flush();
        return $this->render('security/signup-activated.html.twig');
    }


    /**
     * @Route("/reset_password", name="reset_password")
     */
    public function resetPasswordAction(Request $request)
    {
        $authenticationUtils = $this->get('security.authentication_utils');
        $lastUsername = $authenticationUtils->getLastUsername();
        try {
            $user = $this->getDoctrine()->getRepository('AppBundle:User')->loadUserByUsername($lastUsername);
        } catch (UsernameNotFoundException $e) {
            $user = new User();
        }

        $form = $this->createFormBuilder(null, ['csrf_token_id' => 'reset-password'])
                    ->add('usernameOrEmail', TextType::class)->getForm();

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $user = $this->getDoctrine()->getRepository('AppBundle:User')->loadUserByUsername($form->get('usernameOrEmail')->getData());
            $user->activationToken = TokenGeneratorService::generateToken();
            $this->getDoctrine()->getManager()->flush();

            $body = $this->get('templating')
                ->render('email/reset-password.html.twig', ['user' => $user, 'token' => $user->activationToken]);

            $message = \Swift_Message::newInstance()
                ->setTo($user->getEmail())
                ->setFrom($this->getParameter('from_email'))
                ->setSubject('Step Inventory Account Activation')
                ->setContentType('text/html')
                ->setBody($body);

            $this->get('mailer')->send($message);

            return $this->render(
                'security/reset-password.html.twig',
                ['user' => $user]
            );
        }

        return $this->render(
            'security/reset-password.html.twig',
            ['form' => $form->createView(), 'user' => $user]
        );
    }

    /**
     * @Route("/reset_password_update", name="reset_password_update")
     */
    public function resetPasswordUpdateAction(Request $request)
    {
        $formUser = new User();
        if($request->query->has('token')){
            $formUser->activationToken = $request->query->get('token');
        }
        $form = $this->createForm(ResetPasswordUpdateType::class, $formUser);
        $form->handleRequest($request);

        $user = null;
        if($formUser->activationToken){
            $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy([
                'activationToken' => $formUser->activationToken
            ]);
        }
        if(!$user){
            throw $this->createNotFoundException('Password Reset Token '.$token.' Not Found');
        }

        if($form->isSubmitted() && $form->isValid()){
            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $formUser->getPlainPassword());
            $user->setPassword($password);
            $user->activationToken = null;

            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('login_route'));
        }

        return $this->render(
            'security/reset-password-update.html.twig',
            ['form' => $form->createView(), 'user' => $user]
        );
    }

}