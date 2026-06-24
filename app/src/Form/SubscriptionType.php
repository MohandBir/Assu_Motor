<?php

namespace App\Form;

use App\Entity\Subscription;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class SubscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('drivingLicense', FileType::class , [
                'label' => 'Permis de conduire',
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'class' => 'border border-2 border-blue mt-sm-2 ', 
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce document est obligatoire',
                        'groups' => ['validate_docs'], 
                    ]),
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
                        'groups' => ['validate_docs'],
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
                    new NotBlank([
                        'message' => 'Ce document est obligatoire',
                        'groups' => ['validate_docs'],
                    ]),
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'application/pdf' , 
                            'image/jpeg', 
                            'image/png', 
                            'image/webp', 
                        ],
                        'maxSizeMessage' => 'La taille de fichier ne doit pas dépasser 5 MO',
                        'mimeTypesMessage' => 'Formats acceptés : PDF, JPEG, PNG, WEBP',
                        'groups' => ['validate_docs'],
                    ])
                ]
            ])
            ->add('submitDocs', SubmitType::class, [
                'label' => 'Valider mes documents',
                'attr' => ['class' => 'text-center btn btn-outline-blue w-100 text-wrap text-sm-nowrap']
            ])
            ->add('saveAndQuit', SubmitType::class, [
                'label' => 'Compléter plutard',
                'attr' => ['class' => 'text-center btn btn-outline-blue w-100 text-wrap text-sm-nowrap']
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
