<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Votre email',
                'constraints' => new Length([
                    'min'=> 8, 
                    'max' => 60
            ]),
            'attr' => [
                'placeholder' => 'Veuillez saisir votre email'
            ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => "Les 2 mots de passe doivent être identique",
                // 'mapped' => false,
                'options' => [
                    'attr' => ['autocomplete' => 'new-password'],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez saisir votre mot de passe',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                            'max' => 32,
                            'maxMessage' => 'Votre mot de passe ne doit pas dépasser {{ limit }} caractères',
                        ]),
                    ],
                ],
                'required' => true,
                'first_options'  => [
                    'label' => 'Mot de passe',
                    'attr' => [
                        'placeholder' => 'Veuillez saisir votre mot de passe'
                        ]
                    ],
                'second_options' => [
                    'label' => 'Confirmez votre mot de passe',
                    'attr' => [
                    'placeholder' => 'Veuillez confirmer votre mot de passe'
                        ]
                    ],
                    ])
            ->add('firstname', TextType::class, [
                'label' => 'Votre prénom',
                'constraints' => new Length([
                    'min'=> 3, 
                    'max' => 30
                    ]),
                    'attr' => [
                        'placeholder' => 'Veuillez saisir votre prénom'
                            ]
                    ])
            ->add('lastname', TextType::class, [
                'label' => 'Votre nom',
                        'constraints' =>new Length([
                            'min'=> 3, 
                            'max' => 30
                        ]),
                    'attr' => [
                        'placeholder' => 'Veuillez saisir votre nom'
                        ]
                    ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => "Veuillez accepter les conditions d'utilisation",
                'mapped' => false,                            
                'constraints' => [
                    new IsTrue([
                        'message' => "Vous devez acceptez les conditions d'utilisation"
                            ])
                        ]
                    ])
            ->add('submit', SubmitType::class, [
                'label' => "S'inscrire"
                    ]);
                }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
