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
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [ 'rows' => 4,]
            ])
            ->add('level', null, [
                'label' => 'Niveau de difficulté de la figure'
            ])
            ->add('mainPicture', FileType::class, [
                'label' => 'Image Principale ( optionnelle )',
                'required' => false
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
