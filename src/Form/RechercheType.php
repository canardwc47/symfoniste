<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Site;
use App\Form\Models\Recherche;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RechercheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'required' => false,
                'label' => 'Nom de la sortie',
            ])
            ->add('dateDebut', DateType::class, [
                'required' => false,
                'label' => 'Date de la sortie',
                'widget' => 'single_text',
            ])
            ->add('organisateur', CheckboxType::class, [
                'required' => false,
                'label' => 'Sorties dont je suis l\'organisateur',
                'mapped' => false,
            ])
            ->add('participants', CheckboxType::class, [
                'required' => false,
                'label' => 'Sorties auxquelles je suis inscrit',
                'mapped' => false,

            ])
            ->add('nonParticipants', CheckboxType::class, [
                'required' => false,
                'label' => 'Sorties auxquelles je ne suis pas inscrit',
                'mapped' => false,
            ])
            ->add('sorties', CheckboxType::class, [
                'required' => false,
                'label' => 'Sorties passées',
                'mapped' => false,
            ])
            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'required' => false,
                'choice_label' => 'nomLieu',
                'placeholder' => 'Tous les lieux',
                'mapped' => false,

            ])
            ->add('search', SubmitType::class, [
                'label' => 'Rechercher',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([

            'data_class' => Recherche::class,
            'required' => false,
        ]);
    }
}