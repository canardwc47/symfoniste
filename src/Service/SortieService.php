<?php

namespace App\Service;

use App\Controller\SortieController;
use App\Entity\Etat;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class SortieService
{

    private datetime $dateLimiteInscription;
    private SortieController $sortieController;

    public function __construct(SortieRepository $sortieRepository)
    {
        $this->sortieRepository = $sortieRepository;
    }

    public function inscription(
        int $id,
        Security $security,
        EntityManagerInterface $em
        ) : void
    {
        $user = $security->getUser();
        $participant = $em->getRepository(Participant::class)->findOneBy(['id' => $user]);
        $sortie = $em->getRepository(Sortie::class)->find($id);

        $sortie->addParticipant($participant);
        $em->persist($sortie);
        $em->flush();

    }

    public function desistement(
        int $id,
        Security $security,
        EntityManagerInterface $em
    ) : void
    {
        $user = $security->getUser();
        $participant = $em->getRepository(Participant::class)->findOneBy(['id' => $user]);
        $sortie = $em->getRepository(Sortie::class)->find($id);

        $sortie->removeParticipant($participant);
        $em->persist($sortie);
        $em->flush();

    }

    public function publierSortie(
        Sortie $sortie,
        EntityManagerInterface $em
        ) : void
    {
            $etat = $em->getRepository(Etat::class)->findOneBy(['libelle' => 'Ouverte']);
            $sortie->setEtat($etat);
            $em->persist($sortie);
            $em->flush();
    }

    public function clotureSortie (SortieRepository $sortieRepository)
    {


    }



}