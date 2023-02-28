<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('new_password', RepeatedType::class, [
            'type' => PasswordType::class,
            'invalid_message' => "Les 2 mots de passe doivent être identique",
            'options' => [
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre nouveau mot de passe',
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
                'label' => 'Mon nouveau mot de passe',
                'attr' => [
                    'placeholder' => 'Veuillez saisir votre nouveau mot de passe'
                ]
        ],
            'second_options' => [
                'label' => 'Confirmez mon nouveau mot de passe',
                'attr' => [
                    'placeholder' => 'Veuillez confirmer votre nouveau mot de passe'
                ]
        ],
        ])
    ->add('submit', SubmitType::class, [
        'label' => "Mettre à jour",
        'attr' => [
            'class' => 'btn-block btn-primary'
        ]
    ])
    ;
}

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
