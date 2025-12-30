<?php

namespace App\Form;

use App\Entity\Livreur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class LivreurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new NotBlank(['message' => 'Le nom est obligatoire']),
                    new Length(['min' => 2, 'max' => 255])
                ]
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new NotBlank(['message' => 'Le prénom est obligatoire']),
                    new Length(['min' => 2, 'max' => 255])
                ]
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Téléphone',
                'constraints' => [
                    new NotBlank(['message' => 'Le téléphone est obligatoire']),
                    new Length(['min' => 9, 'max' => 20])
                ]
            ])
            ->add('vehicule', ChoiceType::class, [
                'label' => 'Véhicule',
                'choices' => [
                    'Moto' => 'moto',
                    'Voiture' => 'voiture',
                    'Vélo' => 'velo',
                    'Scooter' => 'scooter'
                ],
                'required' => false,
                'placeholder' => 'Sélectionnez un véhicule'
            ])
            ->add('disponible', CheckboxType::class, [
                'label' => 'Disponible',
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Livreur::class,
        ]);
    }
}