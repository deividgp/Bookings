<?php

namespace App\Form;

use App\Entity\Trip;
use Doctrine\DBAL\Types\DateTimeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TripType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextareaType::class, [
                'required' => true,
            ])
            ->add('destination', TextareaType::class, [
                'required' => true,
            ])
            ->add('price',  NumberType::class, [
                'required' => true,
            ])
            ->add('description', TextareaType::class)
            ->add('date')
            ->add('availablePlaces', NumberType::class, [
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Trip::class,
        ]);
    }
}
