<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sorties;
use App\Entity\User;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortiesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la sortie'
            ])
            ->add('dateHeureDebut',DateTimeType::class, [
                'label'=>'Date et heure de la sortie',
                'html5'=>true,
                'widget'=>'single_text',
            ])
            ->add('dateLimiteInscription',DateTimeType::class, [
                'label'=>"Date limite d'inscription",
                'html5'=>true,
                'widget'=>'single_text'
            ])
            ->add('nbInscriptionsMax', TextType::class, [
                'label' => 'Nombre de places'
            ])
            ->add('duree', IntegerType::class, [
                'label' => 'Durée (en minutes)'
            ])
            ->add('infosSortie', TextareaType::class,[
                'label'=>"Description et infos",
            ])
            ->add('ville', EntityType::class, [
                'class' => Ville::class,
                'choice_label' => 'nom',
                'mapped' => false
            ])
            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'choice_label' => 'nom',
            ])
            ->add('rue', EntityType::class, [
                'class' => Lieu::class,
                'mapped' => false,
                'choice_label' => 'rue'
            ])
            ->add('codePostal', EntityType::class, [
                'class' => Ville::class,
                'mapped' => false,
                'choice_label' => 'code_postal'
            ])
            ->add('latitude', EntityType::class, [
                'class' => Lieu::class,
                'mapped' => false,
                'choice_label' => 'latitude'
            ])
            ->add('longitude', EntityType::class, [
                'class' => Lieu::class,
                'mapped' => false,
                'choice_label' => 'longitude'
            ])
            ->add('save', SubmitType::class,[
                'label'=>'Enregistrer'
            ])
            ->add('saveAndAdd', SubmitType::class,[
                'label'=>'Publier une sortie'
            ]);
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sorties::class,
        ]);
    }
}
