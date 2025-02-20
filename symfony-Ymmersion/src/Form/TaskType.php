<?php

namespace App\Form;

use App\Entity\Groups;
use App\Entity\Task;
use App\Entity\Users;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Title')
            ->add('Description')
            ->add('Color', ColorType::class)
            ->add('Periodicity', ChoiceType::class, [
                'choices' => [
                    'Daily' => 'daily',
                    'Weekly' => 'weekly',
                ],
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('Days', ChoiceType::class, [
                'choices' => [
                    'Monday' => 'Monday',
                    'Tuesday' => 'Tuesday',
                    'Wednesday' => 'Wednesday',
                    'Thursday' => 'Thursday',
                    'Friday' => 'Friday',
                    'Saturday' => 'Saturday',
                    'Sunday' => 'Sunday',
                ],
                'expanded' => true,
                'multiple' => true,
                'required' => false,
                'attr' => ['class' => 'weekly-days-selector'],
            ])
            
            ->add('Difficulty', ChoiceType::class, [
                'choices' => [
                    'Very Easy' => 1,
                    'Easy' => 2,
                    'Medium' => 3,
                    'Difficult' => 4,
                ],
            ])
            ->add('isGroupTask', CheckboxType::class, [
                'label' => 'Is a Group Task?',
                'required' => false, // Allow it to be unchecked (false)
            ])
            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
