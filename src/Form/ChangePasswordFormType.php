<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', PasswordType::class, [
                'trim'=>true,
                'invalid_message' => 'Ne correspond pas au mot de passe actuel',
                'required'=>false,
                'label' => 'Ancien mot de passe',
                'label_html' => true,
            ])
            ->add('motDePasse', RepeatedType::class, [
                'type'=> PasswordType::class,
                'trim'=>true,
                'mapped'=>false,
                'invalid_message' => 'Les mots de passe saisis ne correspondent pas.',
                'required'=>false,
                'first_options' => [
                    'label' => 'Nouveau mot de passe',
                    'label_html' => true,
                ],
                'second_options' => ['label' => 'Confirmation du mot de passe : '],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
