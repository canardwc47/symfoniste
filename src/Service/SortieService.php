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

    public function majSorties (
        EntityManagerInterface $em
) : array
    {
        $currentDate = new \DateTimeImmutable();
        $sorties = $em->getRepository(Sortie::class) ->findAll();
        $updatedSorties = [];

        foreach ($sorties as $sortie) {
            if (($sortie->getDateLimiteInscription() >= $currentDate && count($sortie->getParticipants())<=$sortie->getNbInscriptionsMax()) && $sortie->getEtat()->getLibelle() == 'Clôturée' ) {
                $etat = $em->getRepository(Etat::class)->findOneBy(['libelle' => 'Ouverte']);
                $sortie->setEtat($etat);
            }
            if (($sortie->getDateLimiteInscription() <= $currentDate ||count($sortie->getParticipants())>=$sortie->getNbInscriptionsMax()) && $sortie->getEtat()->getLibelle() == 'Ouverte' ) {
                $etat = $em->getRepository(Etat::class)->findOneBy(['libelle' => 'Clôturée']);
                $sortie->setEtat($etat);
            }
            if ($sortie->getDateHeureDebut() <= $currentDate && $sortie->getEtat()->getLibelle() == 'Clôturée' ) {
                $etat = $em->getRepository(Etat::class)->findOneBy(['libelle' => 'Activité en cours']);
                $sortie->setEtat($etat);
            }
            $dateDebutPlusUnJour = $sortie->getDateHeureDebut()->modify('+1 day');
            if ($dateDebutPlusUnJour <= $currentDate && $sortie->getEtat()->getLibelle() == 'Activité en cours' ) {
                $etat = $em->getRepository(Etat::class)->findOneBy(['libelle' => 'Passée']);
                $sortie->setEtat($etat);
            }

            $dateDebutPlusUnMois = $sortie->getDateHeureDebut()->modify('+1 month');
            if ($dateDebutPlusUnMois <= $currentDate && ($sortie->getEtat()->getLibelle() == 'Passée' || $sortie->getEtat()->getLibelle() == 'Annulée'))  {
                $etat = $em->getRepository(Etat::class)->findOneBy(['libelle' => 'Archivée']);
                $sortie->setEtat($etat);
            }
            $em->persist($sortie);
            $updatedSorties[] = $sortie;
        }
        $em->flush();
        return $updatedSorties;

    }

}