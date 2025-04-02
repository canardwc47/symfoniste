<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LieuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomLieu' , null, [
                'label' => 'Nom du lieu',
            ])
            ->add('rue' , null, [
                'label' => 'Adresse',
            ])
            ->add('latitude')
            ->add('longitude')
            ->add('ville', EntityType::class, [
                'class' => Ville::class,
                'choice_label' =>  function (Ville $ville) {
                    return $ville->getNom();
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}
