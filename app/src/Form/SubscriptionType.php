<?php

namespace App\Form;

use App\Entity\Subscription;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class SubscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('documents', FileType::class , [
                'label' => 'Permis de conduire',
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'class' => 'border border-2 border-blue mt-sm-2 ', 
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'application/pdf' , 
                            'image/jpeg', 
                            'imag/png', 
                            'imag/webp', 
                        ],
                        'maxSizeMessage' => 'La taille de fichier ne doit pas dépasser 5 MO',
                        'mimeTypesMessage' => 'Formats acceptés : PDF, JPEG, PNG, WEBP',
                    ])
                ]
            ])
            ->add('grayCard', FileType::class , [
                'label' => 'Carte grise',
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'class' => 'border border-2 border-blue mt-sm-2', 
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'application/pdf' , 
                            'image/jpeg', 
                            'imag/png', 
                            'imag/webp', 
                        ],
                        'maxSizeMessage' => 'La taille de fichier ne doit pas dépasser 5 MO',
                        'mimeTypesMessage' => 'Formats acceptés : PDF, JPEG, PNG, WEBP',
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Subscription::class,
        ]);
    }
}
