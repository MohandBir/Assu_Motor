<?php

namespace App\Form;

use App\Entity\Formula;
use App\Entity\Quote;
use App\Entity\User;
use App\Entity\Vehicle;
use App\Form\VehicleType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;

class QuoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('duration', ChoiceType::class, [
                'label' => 'Durée d\'assurance',
                'choices' => [
                    '1 mois' => '1',
                    '2 mois' => '2',
                    '3 mois' => '3',
                    '6 mois' => '6',
                    '1 ans' => '12',
                ]
            ])
            ->add('licenseYear', ChoiceType::class, [
                'label' => 'Année de permis',
                'choices' => array_combine(
                    range(date('Y'), '1944'),
                    range(date('Y'), '1944')
                ),
            ])
            ->add('birthDate', DateType::class, [
                'label' => 'Date de naissance',
            ])
            ->add('formula', EntityType::class, [
                'class' => Formula::class,
                'choice_label' => 'name',
                'constraints' => [
                    new NotBlank([
                        'message' => 'veuilles sellectionner une formule',
                    ])
                ]
            ])
            ->add('vehicle', VehicleType::class, [
                'label' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Quote::class,
            'label' => false
        ]);
    }
}
