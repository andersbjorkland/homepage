<?php

namespace App\Form;

use App\Entity\Run;
use App\Entity\RunType;
use App\Form\RunTypeFormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

use \DateTime;

class RunFormType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('date', DateType::class, ['label' => "Date"])
            ->add('weightPre', NumberType::class, ['label' => "Weight Pre-run (kg)"])
            ->add('weightPost', NumberType::class, ['label' => "Weight Post-run (kg)"])
            ->add('distance', NumberType::class, ['label' => 'Distance (km)'])
            ->add('time', TimeType::class, [
                'label' => 'Time (hh:mm:ss)',
                'with_seconds' => true 
            ])
            ->add('type', RunTypeFormType::class, ['label' => "Type of run"]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Run::class,
        ]);
    }
}