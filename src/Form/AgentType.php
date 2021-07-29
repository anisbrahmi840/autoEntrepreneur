<?php

namespace App\Form;

use App\Entity\Agent;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class AgentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Saisir votre nom']),
                ],
            ])
            ->add('prenom', TextType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Saisir votre prenom']),
                ],
            ])            
            ->add('cin', IntegerType::class, [                
                'required' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Saisir votre mot de passe']),
                    new Length(['min' => 8,'minMessage' => "mot de passe minimum 8 caractères"]),
                ],
            ])
            ->add('email', EmailType::class, [                
                'required' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Saisir votre Email']),
                ],
            ])
            ->add('password',  RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Mot de passe invalide!',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => false,
                'first_options'  => ['label' => 'Nouveau mot de passe',
                                    'constraints' => [
                                        new NotBlank(['message' => 'Saisir votre mot de passe']),
                                        new Length(['min' => 6, 'minMessage' => "mot de passe minimum 6 caractères"]),
                                    ]],
                'second_options' => ['label' => 'Confirmer  mot de passe'],

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Agent::class,
        ]);
    }
}
