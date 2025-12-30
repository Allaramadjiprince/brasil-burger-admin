<?php

namespace App\Form;

use App\Entity\Paiement;
use App\Entity\Commande;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class PaiementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('commande', EntityType::class, [
                'label' => 'Commande',
                'class' => Commande::class,
                'choice_label' => function(Commande $commande) {
                    return sprintf('#%d - %s (%s XOF)', 
                        $commande->getId(),
                        $commande->getClient() ? $commande->getClient()->getNom() : 'N/A',
                        number_format($commande->getTotal(), 0, ',', ' ')
                    );
                },
                'constraints' => [
                    new NotBlank(['message' => 'La commande est obligatoire'])
                ]
            ])
            ->add('montant', MoneyType::class, [
                'label' => 'Montant',
                'currency' => 'XOF',
                'scale' => 2,
                'constraints' => [
                    new NotBlank(['message' => 'Le montant est obligatoire']),
                    new Positive(['message' => 'Le montant doit être positif'])
                ]
            ])
            ->add('methode', ChoiceType::class, [
                'label' => 'Méthode de paiement',
                'choices' => [
                    'Wave' => Paiement::METHODE_WAVE,
                    'Orange Money' => Paiement::METHODE_OM
                ],
                'constraints' => [
                    new NotBlank(['message' => 'La méthode est obligatoire'])
                ]
            ])
            ->add('statut', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => [
                    'En attente' => Paiement::STATUT_EN_ATTENTE,
                    'Payé' => Paiement::STATUT_PAYE,
                    'Échec' => Paiement::STATUT_ECHEC
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le statut est obligatoire'])
                ]
            ])
            ->add('date', DateTimeType::class, [
                'label' => 'Date du paiement',
                'widget' => 'single_text',
                'html5' => true,
                'constraints' => [
                    new NotBlank(['message' => 'La date est obligatoire'])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Paiement::class,
        ]);
    }
}