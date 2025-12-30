<?php

namespace App\Form;

use App\Entity\ZoneLivraison;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class ZoneLivraisonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la zone',
                'constraints' => [
                    new NotBlank(['message' => 'Le nom est obligatoire'])
                ]
            ])
            ->add('quartiers', TextareaType::class, [
                'label' => 'Quartiers couverts',
                'attr' => ['rows' => 5],
                'constraints' => [
                    new NotBlank(['message' => 'Les quartiers sont obligatoires'])
                ],
                'help' => 'Séparez les noms des quartiers par des virgules'
            ])
            ->add('prix', MoneyType::class, [
                'label' => 'Prix de livraison',
                'currency' => 'XOF',
                'scale' => 2,
                'constraints' => [
                    new NotBlank(['message' => 'Le prix est obligatoire']),
                    new Positive(['message' => 'Le prix doit être positif'])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ZoneLivraison::class,
        ]);
    }
}