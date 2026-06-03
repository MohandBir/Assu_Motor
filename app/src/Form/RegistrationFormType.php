<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class , [
                'constraints' => [
                    new NotBlank(
                        message: 'Le champ doit être rempli',
                    ),
                    new Length(
                        min: 10,
                        max: 180,
                        minMessage: 'Ce champ doit avoir au moins {{ limit }} caractères',
                        maxMessage: 'Ce champ ne doit pas dépasser {{ limit }} caractères',
                    )
                    ]
            ])
            ->add('lname', TextType::class , [               
                'label' => 'Nom',
                'constraints' => [
                    new NotBlank(
                        message: 'Le champ doit être rempli',
                    ),
                    new Length(
                        min: 3,
                        max: 50,
                        minMessage: 'Ce champ doit avoir au moins {{ limit }} caractères',
                        maxMessage: 'Ce champ ne doit pas dépasser {{ limit }} caractères',
                    )
                    ]
            ])
            ->add('fname', TextType::class , [
                'label' => 'Prénom',
                'constraints' => [
                    new NotBlank(
                        message: 'Le champ doit être rempli',
                    ),
                    new Length(
                        min: 3,
                        max: 50,
                        minMessage: 'Ce champ doit avoir au moins {{ limit }} caractères',
                        maxMessage: 'Ce champ ne doit pas dépasser {{ limit }} caractères',
                    )
                    ]
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'label' => 'Mot de passe',
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank(
                        message: 'Please enter a password',
                    ),
                    new Length(
                        min: 12,
                        minMessage: 'Le mot de passe doit contenir au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        max: 4096,
                    ),
                    new Regex(
                        pattern: "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{12,}$/",
                        message: "le mot de passe doit contenir 12 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial."
                    )
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
