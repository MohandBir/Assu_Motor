<?php

namespace App\Form;

use App\Entity\Document;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('license', FileType::class , [
                'label' => 'Permis de conduire',
                'required' => false,
                'attr' => [
                    'class' => 'border border-2 border-blue mt-sm-2 ', 
                ]
            ])
            ->add('grayCard', FileType::class , [
                'label' => 'Carte grise',
                'required' => false,
                'attr' => [
                    'class' => 'border border-2 border-blue mt-sm-2', 
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Document::class,
        ]);
    }
}
