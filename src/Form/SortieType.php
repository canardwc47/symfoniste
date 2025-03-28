<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Site;
use App\Entity\Sortie;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType implements FormTypeInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomSortie')
            ->add('dateHeureDebut', DateTimeType::class, [
                'widget' => 'single_text',

               // 'format' => 'yyyy-MM-dd HH:mm',
            ])
            ->add('duree')
           ->add('dateLimiteInscription', null, [
                'widget' => 'single_text',
            ])
            ->add('nbInscriptionsMax')
            ->add('infosSortie')

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
                'choice_label' =>  function (Lieu $lieu) {
                    return $lieu->getVille() . ' - ' . $lieu->getNomLieu();
                    },
                    'query_builder' => function (EntityRepository $er) {
        return $er->createQueryBuilder('l')
            ->join('l.ville', 'v')
            ->orderBy('v.nom', 'ASC'); // Make sure 'nom' is the correct property for the city name
    },

            ])

        ;
            dump($builder);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
