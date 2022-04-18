<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Trick;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class TrickAddType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'Categorie'
            ])
            ->add('level', ChoiceType::class, [
                'label' => 'Niveau de difficulté de la figure ',
                'choices' => [
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,
                    '5' => 5,
                ],
                'help' => 'Sur une echelle de 1 à 5 ; facile = 1, difficile = 5'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => ['rows' => 4,]
            ])
            ->add('setMainPicture', FileType::class, [
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
            ->add('videos', CollectionType::class, [
                'label' => 'Videos d\'illustrations ( optionnel )',
                'entry_type' => VideoType::class,
                'required' => false,
                'empty_data' => null,
                'entry_options' => ['label' => false, 'empty_data' => null,],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false
            ])
            ->add('pictures', CollectionType::class, [
                'label' => 'Images d\'illustrations ( optionnel )',
                'entry_type' => PictureType::class,
                'required' => false,
                'mapped' => false,
                'entry_options' => ['label' => false, 'empty_data' => null,],
                'empty_data' => null,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
