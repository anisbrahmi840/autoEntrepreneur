<?php

namespace App\Form;

use App\Entity\Agent;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class AgentEditPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword', PasswordType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Mot de passe actuel',
                'constraints' => [
                    new NotNull(['message' => 'Saisir votre mot de passe']),
                    new Length(['min' => 6, 'minMessage' => "mot de passe minimum 6 caractères"]),
                ]
            ])
            ->add('password',  RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => false,
                'invalid_message' => 'Mot de passe invalide!',
                'options' => ['attr' => ['class' => 'password-field']],
                'first_options'  => ['label' => 'Nouveau mot de passe',
                'constraints' => [
                    new NotNull(['message' => 'Saisir votre mot de passe']),
                    new Length(['min' => 6, 'minMessage' => "mot de passe minimum 6 caractères"]),
                ]],
                'second_options' => ['label' => 'Confirmer  mot de passe'],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}
