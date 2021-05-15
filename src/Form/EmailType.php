<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType as TypeEmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class EmailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Saisir votre nom'])
                ],
                'attr' => [
                    'placeholder' => 'Prenom, Nom'
                ]
            ])
            ->add('email' , TextType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Saisir votre email']),
                    new Email(['message' => 'Saisir une email valid'])
                ],
                'attr' => [
                    'placeholder' => 'Email'
                ]
            ])
            ->add('sujet', TextType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Saisir votre sujet'])
                ],
                'attr' => [
                    'placeholder' => 'Sujet'
                ]
            ])
            ->add('message', TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Saisir votre message'])
                ],
                'attr' => [
                    'placeholder' => 'Votre message ici',
                    'rows' => '7'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
