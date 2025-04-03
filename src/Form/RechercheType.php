<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Site;
use App\Form\Models\Recherche;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class RechercheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'required' => false,
                'label' => 'Nom de la sortie',
                'attr' => [
                    'placeholder' => 'Rechercher par nom',
                    'class' => 'form-control'
                ],

            ])
            ->add('dateDebut', DateType::class, [
                'required' => false,
                'label' => 'Date de début',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control js-datepicker'
                ],
                'html5' => true
            ])
            ->add('dateFin', DateType::class, [
                'required' => false,
                'label' => 'Date de fin',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control js-datepicker'
                ],
                'html5' => true
            ])
            ->add('organisateur', CheckboxType::class, [
                'required' => false,
                'label' => 'Sorties dont je suis l\'organisateur',
                'attr' => [
                    'class' => 'form-check-input'
                ]
            ])
            ->add('participant', CheckboxType::class, [
                'required' => false,
                'label' => 'Sorties auxquelles je suis inscrit',
                'attr' => [
                    'class' => 'form-check-input'
                ]
            ])
            ->add('nonParticipant', CheckboxType::class, [
                'required' => false,
                'label' => 'Sorties auxquelles je ne suis pas inscrit',
                'attr' => [
                    'class' => 'form-check-input'
                ]
            ])
            ->add('sortiesPassees', CheckboxType::class, [
                'required' => false,
                'label' => 'Sorties passées',
                'attr' => [
                    'class' => 'form-check-input'
                ]
            ])
            ->add('site', EntityType::class, [
                'class' => Site::class,
                'required' => false,
                'choice_label' => 'nomSite',
                'placeholder' => 'Tous les sites',
                'attr' => [
                    'class' => 'form-select'
                ]
            ])
            ->add('search', SubmitType::class, [
                'label' => 'Rechercher',
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Recherche::class,
            'required' => false,
            'csrf_protection' => true,
            'validation_groups' => ['Default'],
        ]);
    }
}