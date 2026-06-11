<?php

namespace App\Form;

use App\Entity\Brand;
use App\Entity\Model;
use App\Entity\Vehicle;
use App\Repository\BrandRepository;
use App\Repository\ModelRepository;
use Doctrine\Common\Collections\Placeholder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class VehicleType extends AbstractType 
{
    public function __construct(
        private BrandRepository $brandRepo,
        private ModelRepository $modelRepo,
    ){}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $brandChoices = [];
        foreach (($this->brandRepo->findAll()) as $brand) {
            $brandChoices[$brand->getName()] = $brand->getName();
        }
        $modelChoices = [];
        foreach (($this->modelRepo->findAll()) as $model) {
            $modelChoices[$model->getName()] = $model->getName();
        }


        $builder
            ->add('type', ChoiceType::class, [
                'label' => 'Type',
                'choices' => [
                    'Voiture' => 'car',
                    'Moto' => 'moto',
                    'Utilitaire' => 'truck',
                ],
                'choice_attr' => function ($choice, $key, $value) {
                    if (in_array($value, ['moto', 'truck'])) {
                        return ['disabled' => 'disabled'];
                    }
                    return [];
                },
                'constraints' => [
                    new NotBlank([
                        'message' => 'veuilles sellectionner un Type',
                    ]),
                    new Choice([
                        'choices' => ['car', 'moto', 'truck',],
                    ])
                ] 
            ])
            ->add('brand' , ChoiceType::class, [
                'label' => 'Marque',
                'choices' => $brandChoices,
                'placeholder' => 'Choisissez la marque',
                'constraints' => [
                    new NotBlank([
                        'message' => 'veuilles sellectionner une marque',
                    ]),
                ]
            ])
            ->add('model' , ChoiceType::class, [
                'label' => 'Model',
                'choices' => $modelChoices,
                'placeholder' => 'Choisissez la model',
                'constraints' => [
                    new NotBlank([
                        'message' => 'veuilles sellectionner un model',
                    ]),
                ]
            ])
            ->add('vehicleYear', ChoiceType::class,[
                'label' => 'Année',
                'choices' => array_combine(
                    range(date('Y'), '1990'),
                    range(date('Y'), '1990')
                ),
                'constraints' => [
                    new NotBlank([
                        'message' => 'veuilles sellectionner une Année valide',
                    ]),
                    new Choice([
                        'choices' => range(date('Y'), '1944'),
                    ])
                ],
            ])    
            ->add('licensePlate', TextType::class, [
                'label' => 'Immatriculation',
                'label_attr' => [
                    'class' => 'text-break',
                ],
                'attr' => [
                    'class' => 'text-uppercase',
                    'placeholder' => 'AA-123-AA.',
                    'maxlength' => 9,
                ],
                
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[A-Za-z]{2}-\d{3}-[A-Za-z]{2}$/',
                        'message' => 'L\'immatriculation doit être au format AA-123-AA.',
                    ])
                ]
            ])
                    
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Vehicle::class,
        ]);
    }
}
