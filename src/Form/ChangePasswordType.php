<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options = []): void
    {
        if ($options['current_password_is_required'])
        {
            $builder
                ->add('currentPassword', PasswordType::class, [
                    'label' => 'Entrer votre mot de passe actuel',
                    'attr' => [
                        'autocomplete' => 'off'
                    ],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Entrer votre mot de passe actuel',
                        ]),
                        new UserPassword(['message' => 'Mot de passe actuel invalide']),
                    ]]);
        }
        $builder
            ->add('plainPassword', RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'first_options' => [
                        'attr' => ['autocomplete' => 'new-password'],
                        'constraints' => [
                            new NotBlank([
                                'message' => 'Veuillez entrer votre mot de passe',
                            ]),
                            new Length([
                                'min' => 6,
                                'minMessage' => 'Votre mot de passe doit avoir  {{ limit }} caractères minimum',
                                'max' => 4096,
                            ]),
                        ],
                        'label' => 'Entrer votre nouveau mot de passe',
                    ],
                    'second_options' => [
                        'attr' => ['autocomplete' => 'new-password'],
                        'label' => 'Repeter votre nouveau mot de passe',
                    ],
                    'invalid_message' => 'Les deux mots de passe ne sont pas identiques !',
                    'mapped' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'current_password_is_required' => false
        ]);
        $resolver->addAllowedTypes('current_password_is_required', 'bool');
    }
}