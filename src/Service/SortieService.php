<?php

namespace App\Service;

use App\Controller\SortieController;
use App\Entity\Etat;
use App\Entity\Sortie;
use App\Repository\SortieRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class SortieService
{

    private datetime $dateLimiteInscription;
    private SortieRepository $sortieRepository;
    private SortieController $sortieController;

    public function __construct(SortieRepository $sortieRepository)
    {
        $this->sortieRepository = $sortieRepository;
    }

    public function publierSortie(
        Sortie $sortie,
        EntityManagerInterface $em
        ) : void
    {
            //$etat = $em->getRepository(Etat::class)->findOneBy(['libelle' => 'Ouverte']);
            $etat = $em->getRepository(Etat::class)->find(2);
            $sortie->setEtat($etat);
            $em->persist($sortie);
            $em->flush();
    }

    public function clotureSortie (SortieRepository $sortieRepository)
    {

    }



}