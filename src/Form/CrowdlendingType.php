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
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
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
                    'min' => 0,
                    'placeholder' => 'Montant investi',
                ],
                'html5' => true,
                'label' => 'Montant investi',
            ])
            ->add('currentValue', NumberType::class, [
                'attr' => [
                    'min' => 0,
                    'placeholder' => 'Valeur actuelle'
                ],
                'html5' => true,
                'label' => 'Valeur actuelle',
            ])
            ->add('duration', NumberType::class, [
                'attr' => [
                    'min' => 0,
                    'placeholder' => 'Durée'
                ],
                'html5' => true,
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
                    'min' => 0,
                    'placeholder' => "Rendement cible"
                ],
                'html5' => true,
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

    public function getBlockPrefix(): string
    {
        return '';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Crowdlending::class,
        ]);
    }
}