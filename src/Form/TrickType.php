<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Trick;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class, [
                'label' => 'Nom'
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'Categorie'
            ])
            ->add('level', null, [
                'label' => 'Niveau de difficulté de la figure ',
                'help' => 'Sur une echelle de 1 à 5 ; facile = 1, difficile = 5'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [ 'rows' => 4,]
            ])
            ->add('setPicture', FileType::class, [
                'label' => 'Image Principale ( optionnelle )',
                'help' => 'Formats : png, jpg, jpeg.   Taille : max 8Mo ',
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '9000k',       // 1024 kB = 1 MB, 8192 kB = 8 MB
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/jpg'
                        ],
                        'mimeTypesMessage' => 'Seuls les formats jpg, png et jpeg sont acceptés',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
