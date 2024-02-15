<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;


class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('oldPassword', PasswordType::class, [
                'trim'=>true,
                'required'=>false,
                'mapped' => false,
                'label' => 'Ancien mot de passe',
                'label_html' => true,
                'constraints' => [new SecurityAssert\UserPassword(message: "Le mot de passe ne correspond pas au mot de passe actuel")]
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
