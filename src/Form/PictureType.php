<?php

namespace App\Form;

use App\Entity\Picture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class PictureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('setCollectionPicture', FileType::class, [
                'label' => 'Ajouter une image pour cette figure',
                'help' => 'Formats : png, jpg, jpeg.   Taille : max 1Mo par image',
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2000k',       // 1024 kB = 1 MB, 8192 kB = 8 MB
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/jpg'
                        ],
                        'mimeTypesMessage' => 'Seuls les formats jpg, png et jpeg sont acceptés',
                    ])
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Picture::class,
        ]);
    }
}
