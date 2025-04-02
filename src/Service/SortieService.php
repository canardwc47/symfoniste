<?php

namespace App\Service;

use App\Controller\SortieController;
use App\Entity\Etat;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class SortieService
{
    private datetime $dateLimiteInscription;
    private SortieController $sortieController;

    public function __construct(
        SortieRepository $sortieRepository,
        ParticipantRepository $participantRepository,
        EtatRepository $etatRepository)
    {
        $this->sortieRepository = $sortieRepository;
        $this->participantRepository = $participantRepository;
        $this->etatRepository = $etatRepository;
    }

    public function inscription(
        int $id,
        Security $security,
        EntityManagerInterface $em,
        ParticipantRepository $participantRepository,
        SortieRepository $sortieRepository
        ) : String
    {
        // Conditions à remplir, message d'erreur ciblé suivant la condition non remplie
        $user = $security->getUser();
        if (!$user) {
            return "Utilisateur non connecté.";
        }
        $participant = $participantRepository->findOneBy(['id' => $user]);
        $sortie = $sortieRepository->find($id);

        if (!$participant || !$sortie) {
            return "Sortie ou participant introuvable.";
        }
        if ($sortie->getEtat()->getLibelle() !== 'Ouverte') {
            return "La sortie n'est pas ouverte.";
        }
        $currentDate = new \DateTimeImmutable();
        if ($sortie->getDateLimiteInscription() < $currentDate) {
            return "La date limite d'inscription est dépassée.";
        }
        if ($sortie->getParticipants()->count() >= $sortie->getNbInscriptionsMax()) {
            return "Le nombre maximum de participants est atteint.";
        }
        if ($sortie->getParticipants()->contains($participant)) {
            return "Vous êtes déjà inscrit(e) à cette sortie.";
        }
        $sortie->addParticipant($participant);
        $em->persist($sortie);
        $em->flush();
        return "Inscription réussie.";
    }

    public function desistement(
        int $id,
        Security $security,
        EntityManagerInterface $em,
        ParticipantRepository $participantRepository,
        SortieRepository $sortieRepository
    ) : String
    {
        // Conditions à remplir, message d'erreur ciblé suivant la condition non remplie
        $user = $security->getUser();
        if (!$user) {
            return "Utilisateur non connecté.";
        }
        $participant = $participantRepository->findOneBy(['id' => $user]);
        $sortie = $sortieRepository->find($id);
        if (!$participant || !$sortie) {
            return "Sortie ou participant introuvable.";
        }
        if (!$sortie->getParticipants()->contains($participant)) {
            return "Vous n'êtes pas inscrit(e) à cette sortie.";
        }
        if ($sortie->getOrganisateur()===($participant)) {
            return "L'organisateur ne peut pas se désister de sa sortie.";
        }
        $currentDate = new \DateTimeImmutable();
        if ($sortie->getDateHeureDebut() < $currentDate) {
            return "La date de la sortie est passée.";
        }
        $etat = $sortie->getEtat()->getLibelle();
        if ($etat === 'Ouverte' || $etat === 'Clôturée') {
            $sortie->removeParticipant($participant);
            $em->persist($sortie);
            $em->flush();
            return "Désistement enregistré.";
        }
        return "Action impossible.";
    }

    public function publierSortie(
        Sortie $sortie,
        EntityManagerInterface $em,
        EtatRepository $etatRepository
        ) : void
    {
            $etat = $etatRepository->findOneBy(['libelle' => 'Ouverte']);
            $sortie->setEtat($etat);
            $em->persist($sortie);
            $em->flush();
    }

    // Mise à jour des sorties en fonction de la date et/ou du nombre de participants,
    public function majSorties (
        EntityManagerInterface $em,
        SortieRepository $sortieRepository,
        EtatRepository $etatRepository
) : array
    {
        $currentDate = new \DateTimeImmutable();
        $sorties = $sortieRepository->findAll();
        $updatedSorties = [];

        foreach ($sorties as $sortie) {
            $etatOrig = $sortie->getEtat()->getLibelle();
            if (
                ($sortie->getDateLimiteInscription() >= $currentDate && $sortie->getParticipants()->count()<=$sortie->getNbInscriptionsMax())
                && $etatOrig == 'Clôturée' ) {
                $etat = $etatRepository->findOneBy(['libelle' => 'Ouverte']);
                $sortie->setEtat($etat);
            }
            if (($sortie->getDateLimiteInscription() <= $currentDate ||$sortie->getParticipants()->count()>=$sortie->getNbInscriptionsMax())
                && $etatOrig == 'Ouverte' ) {
                $etat = $etatRepository->findOneBy(['libelle' => 'Clôturée']);
                $sortie->setEtat($etat);
            }
            if ($sortie->getDateHeureDebut() <= $currentDate && $etatOrig == 'Clôturée' ) {
                $etat = $etatRepository->findOneBy(['libelle' => 'Activité en cours']);
                $sortie->setEtat($etat);
            }
            $dateDebutPlusUnJour = $sortie->getDateHeureDebut()->modify('+1 day');
            if ($dateDebutPlusUnJour <= $currentDate && $etatOrig == 'Activité en cours' ) {
                $etat = $etatRepository->findOneBy(['libelle' => 'Passée']);
                $sortie->setEtat($etat);
            }

            $dateDebutPlusUnMois = $sortie->getDateHeureDebut()->modify('+1 month');
            if ($dateDebutPlusUnMois <= $currentDate && ($etatOrig == 'Passée' || $etatOrig == 'Annulée'))  {
                $etat = $etatRepository->findOneBy(['libelle' => 'Archivée']);
                $sortie->setEtat($etat);
            }
            $em->persist($sortie);
            $updatedSorties[] = $sortie;
        }
        $em->flush();
        return $updatedSorties;

    }

}