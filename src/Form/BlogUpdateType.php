<?php

namespace App\Form;

use App\Entity\BlogPost;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class BlogUpdateType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('title')
            ->add('text')
            ->add('publishTime', DateTimeType::class, [
                'label' => 'Publish'
            ])
            ->add('blogImages', CollectionType::class, [
                'entry_type' => BlogImageType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'mapped' => false,
                'by_reference' => false
            ])
            ->add('categories', CollectionType::class, [
                'entry_type' => CategoryType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'mapped' => false,
                'by_reference' => false
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BlogPost::class,
        ]);
    }
}