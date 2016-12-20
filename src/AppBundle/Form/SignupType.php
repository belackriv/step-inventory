<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\IsTrue;

class SignupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class)
            ->add('username', TextType::class)
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('plainPassword', RepeatedType::class, ['type' => PasswordType::class])
            ->add('organizationName', TextType::class, [
                'mapped' => false,
                'constraints' => new NotBlank(),
            ])
            ->add('termsAccepted', CheckboxType::class, [
                'mapped' => false,
                'constraints' => new IsTrue(),
            ])
            ->add('plan', EntityType::class, [
                'class' => 'AppBundle:Plan',
                'choice_label' => function($plan) {
                    return $plan->getName() .' - '. $plan->getDescription();
                },
                'query_builder' => function(\Doctrine\ORM\EntityRepository $er){
                    return $er->createQueryBuilder('p')
                        ->where('p.isActive = :true')
                        ->setParameter('true', true)
                        ->orderBy('p.name', 'ASC');
                },
                'mapped' => false,
                'constraints' => new NotNull(),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
        ));
    }
}