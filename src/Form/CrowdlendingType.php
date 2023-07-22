<?php

namespace App\Form;

use App\Entity\Crowdlending;
use App\Entity\CrowdlendingPlatform;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CrowdlendingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => [
                    'placeholder' => 'Nom'
                ],
                'label' => 'Nom',
            ])
            ->add('investedAmount', NumberType::class, [
                'attr' => [
                    'placeholder' => 'Montant investi'
                ],
                'label' => 'Montant investi',
            ])
            ->add('currentValue', NumberType::class, [
                'attr' => [
                    'placeholder' => 'Valeur actuelle'
                ],
                'label' => 'Valeur actuelle',
            ])
            ->add('duration', NumberType::class, [
                'attr' => [
                    'placeholder' => 'Durée'
                ],
                'label' => 'Durée',
                'required' => false
            ])
            ->add('investmentDate', DateType::class, [
                'attr' => [
                    'placeholder' => "Date d'investissement"
                ],
                'label' => "Date d'investissement",
                'required' => false,
                'widget' => 'single_text'
            ])
            ->add('annualYield', NumberType::class, [
                'attr' => [
                    'placeholder' => "Rendement cible"
                ],
                'label' => "Rendement cible",
                'required' => false
            ])
            ->add('platform', EntityType::class, [
                'attr' => [
                    'placeholder' => "Plateforme"
                ],
                'class' => CrowdlendingPlatform::class,
                'label' => 'Plateforme'
            ])
            ->add('submit', SubmitType::class, [
            'label' => 'Valider'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Crowdlending::class,
        ]);
    }
}