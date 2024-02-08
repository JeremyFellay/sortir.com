<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
        //    ->add('roles')
        //    ->add('password')
            ->add('pseudo')
            ->add('prenom')
            ->add('nom')
            ->add('telephone')
            ->add('campus', ChoiceType::class, [
                'choices' => [
                    'Rennes' => 'Rennes',
                    'Nantes' => 'Nantes',
                    'Niort' => 'Niort',
                    'Quimper' => 'Quimper',
                    'En ligne' => 'En ligne'
                ],
                'multiple' => false
            ])
        //    ->add('photo')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
