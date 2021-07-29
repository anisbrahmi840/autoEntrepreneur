<?php

namespace App\Form;

use App\Entity\Activite;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ActiviteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => "Nom de l'activité",
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Saisir un nom'])
                ]
            ])
            ->add('taux', NumberType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => "Taux de cotisation"
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Saisir taux de cotisation'])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Activite::class,
        ]);
    }
}
