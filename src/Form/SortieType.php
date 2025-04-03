<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\Sortie;
use DateTime;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomSortie', TextType::class, [
                'label' => 'Nom de la sortie',
                'attr' => [
                    'placeholder' => 'Saisissez un nom pour la sortie',
                    'minlength' => 2,
                    'maxlength' => 50
                ],
//                'constraints' => [
//                    new NotBlank(['message' => 'Le nom de la sortie est obligatoire']),
//                    new Length([
//                        'min' => 2,
//                        'max' => 50,
//                        'minMessage' => 'Le nom de la sortie doit contenir au moins 2 caractères',
//                        'maxMessage' => 'Le nom de la sortie ne peut pas dépasser 50 caractères'
//                    ])
//                ]
            ])
            ->add('dateHeureDebut', DateTimeType::class, [
                'label' => 'Date et heure de la sortie',
                'widget' => 'single_text',
/*                'constraints' => [
                    new NotBlank(['message' => 'La date de la sortie est obligatoire']),
                    new GreaterThan([
                        'value' => 'today',
                        'message' => 'La date de la sortie doit être dans le futur'
                    ])
                ]*/
            ])
            ->add('duree', IntegerType::class, [
                'label' => 'Durée (en minutes)',
                'attr' => ['min' => 1],
/*                'constraints' => [
                    new NotBlank(['message' => 'La durée est obligatoire']),
                    new Positive(['message' => 'La durée doit être un nombre positif'])
                ]*/
            ])
            ->add('dateLimiteInscription', DateTimeType::class, [
                'label' => 'Date limite d\'inscription',
                'widget' => 'single_text',
/*                'constraints' => [
                    new NotBlank(['message' => 'La date limite d\'inscription est obligatoire']),
                    new LessThan([
                        'propertyPath' => 'parent.all[dateHeureDebut].data',
                        'message' => 'La date limite d\'inscription doit être avant la date de début de la sortie'
                    ]),
                    new GreaterThanOrEqual([
                        'value' => 'today',
                        'message' => 'La date limite d\'inscription doit être aujourd\'hui ou dans le futur'
                    ])
                ]*/
            ])
            ->add('nbInscriptionsMax', IntegerType::class, [
                'label' => 'Nombre maximum de participants',
                'attr' => [
                    'min' => 1,
                    'max' => 999
                ],
/*                'constraints' => [
                    new NotBlank(['message' => 'Le nombre maximum d\'inscriptions est obligatoire']),
                    new Positive(['message' => 'Le nombre d\'inscriptions doit être supérieur à 0']),
                    new LessThan([
                        'value' => 1000,
                        'message' => 'Le nombre maximum d\'inscriptions ne peut pas dépasser 999'
                    ])
                ]*/
            ])
            ->add('infosSortie', TextareaType::class, [
                'label' => 'Description de la sortie',
                'attr' => [
                    'placeholder' => 'Décrivez la sortie en détails',
                    'minlength' => 10,
                    'maxlength' => 250,
                    'rows' => 5
                ],
/*                'constraints' => [
                    new NotBlank(['message' => 'La description de la sortie est obligatoire']),
                    new Length([
                        'min' => 10,
                        'max' => 250,
                        'minMessage' => 'La description de la sortie doit contenir au moins 10 caractères',
                        'maxMessage' => 'La description de la sortie ne peut pas dépasser 250 caractères'
                    ])
                ]*/
            ])

            /*
             * Liste des participants de la sortie
             * ->add('participants', EntityType::class, [
                'class' => Participant::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
             ->add('site', EntityType::class, [
                'class' => Site::class,
                'choice_label' => 'nomSite',
            ])

            */
            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'label' => 'Lieu de la sortie',
                'choice_label' =>  function (Lieu $lieu) {
                    return $lieu->getVille() . ' - ' . $lieu->getNomLieu();
                },
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('l')
                        ->join('l.ville', 'v')
                        ->orderBy('v.nom', 'ASC')
                        ->addOrderBy('l.nomLieu', 'ASC');
                },
/*                'constraints' => [
                    new NotNull(['message' => 'Le lieu de la sortie doit être défini'])
                ],*/
                'placeholder' => 'Choisissez un lieu'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
            'organisateur' => null,
        ]);
    }
}