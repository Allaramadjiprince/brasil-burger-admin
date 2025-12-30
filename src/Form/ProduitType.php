<?php

namespace App\Form;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du produit',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Brasil Burger Classic'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le nom est obligatoire'])
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 3,
                    'placeholder' => 'Décrivez votre produit...'
                ]
            ])
            ->add('prix', MoneyType::class, [
                'label' => 'Prix (XOF)',
                'currency' => 'XOF',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '0.00'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le prix est obligatoire']),
                    new Positive(['message' => 'Le prix doit être positif'])
                ]
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type de produit',
                'choices' => [
                    'Burger' => Produit::TYPE_BURGER,
                    'Menu' => Produit::TYPE_MENU,
                    'Complément' => Produit::TYPE_COMPLEMENT
                ],
                'attr' => [
                    'class' => 'form-select'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le type est obligatoire'])
                ]
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Image du produit',
                'required' => false,
                'mapped' => false, // Important : pas directement lié à l'entité
                'attr' => [
                    'class' => 'form-control'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}