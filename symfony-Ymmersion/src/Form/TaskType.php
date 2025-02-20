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
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Title', TextType::class, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('Description', TextareaType::class, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('Color', ColorType::class, [
                'attr' => ['class' => 'form-control'],
            ])
            ->add('Periodicity', ChoiceType::class, [
                'choices' => [
                    'Daily' => 'daily',
                    'Weekly' => 'weekly',
                ],
                'expanded' => true,
                'multiple' => false,
                'attr' => ['class' => 'form-check-inline'],
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
                'attr' => ['class' => 'weekly-days-selector form-check-inline'],
            ])
            
            ->add('Difficulty', ChoiceType::class, [
                'choices' => [
                    'Very Easy' => 1,
                    'Easy' => 2,
                    'Medium' => 3,
                    'Difficult' => 4,
                ],
                'attr' => ['class' => 'form-control'],
            ])
            ->add('isGroupTask', CheckboxType::class, [
                'label' => 'Is a Group Task?',
                'required' => false,
                'attr' => ['class' => 'form-check-input'],
            ])
            ->add('save', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary'],
            ])
        ;

        $builder->get('Days')->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            if (is_string($data)) {
                $event->setData(explode(',', $data));
            }
        });

        $builder->get('Days')->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $data = $event->getData();
            if (is_string($data)) {
                $event->setData(explode(',', $data));
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
