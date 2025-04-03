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
        Security $security,
        SortieRepository $sortieRepository,
        ParticipantRepository $participantRepository,
        EtatRepository $etatRepository,
        EntityManagerInterface $em)
    {
        $this->security = $security;
        $this->sortieRepository = $sortieRepository;
        $this->participantRepository = $participantRepository;
        $this->etatRepository = $etatRepository;
        $this->em = $em;
    }

    public function inscription(
        int $id,
       ) : String
    {
        // Conditions à remplir, message d'erreur ciblé suivant la condition non remplie
        $user = $this->security->getUser();
        if (!$user) {
            return "Utilisateur non connecté.";
        }

        $participant = $this->participantRepository->find($user->getId());
        $sortie = $this->sortieRepository->find($id);

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
        $this->em->persist($sortie);
        $this->em->flush();
        return "Inscription réussie.";
    }

    public function desistement(
        int $id,
    ) : String
    {
        // Conditions à remplir, message d'erreur ciblé suivant la condition non remplie
        $user = $this->security->getUser();
        if (!$user) {
            return "Utilisateur non connecté.";
        }

        $participant = $this->participantRepository->find($user->getId());
        $sortie = $this->sortieRepository->find($id);

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
            $this->em->persist($sortie);
            $this->em->flush();
            return "Désistement enregistré.";
        }
        return "Action impossible.";
    }

    public function publierSortie(
        Sortie $sortie,
        ) : void
    {
            $em = $this->em;
            $etat = $this->etatRepository->findOneBy(['libelle' => 'Ouverte']);
            $sortie->setEtat($etat);
            $em->persist($sortie);
            $em->flush();
    }

    // Mise à jour des sorties en fonction de la date et/ou du nombre de participants,
    public function majSorties () : void
    {
        $em = $this->em;
        $etatRepository = $this->etatRepository;
        $currentDate = new \DateTimeImmutable();
        $sorties = $this->sortieRepository->findAll();
        $etats = [
            'Ouverte' => $etatRepository->findOneBy(['libelle' => 'Ouverte']),
            'Clôturée' => $etatRepository->findOneBy(['libelle' => 'Clôturée']),
            'Activité en cours' => $etatRepository->findOneBy(['libelle' => 'Activité en cours']),
            'Passée' => $etatRepository->findOneBy(['libelle' => 'Passée']),
            'Archivée' => $etatRepository->findOneBy(['libelle' => 'Archivée']),
        ];

        foreach ($sorties as $sortie) {
            $etatOrig = $sortie->getEtat()->getLibelle();
            if (
                ($sortie->getDateLimiteInscription() >= $currentDate && count($sortie->getParticipants())<$sortie->getNbInscriptionsMax())
                && $etatOrig == 'Clôturée' ) {
                $sortie->setEtat($etats['Ouverte']);
                $em->persist($sortie);
                continue;
            }
            if (($sortie->getDateLimiteInscription() <= $currentDate || count($sortie->getParticipants())>=$sortie->getNbInscriptionsMax())
                && $etatOrig == 'Ouverte' ) {
                $sortie->setEtat($etats['Clôturée']);
                $em->persist($sortie);
                continue;
            }
            if ($sortie->getDateHeureDebut() <= $currentDate && $etatOrig == 'Clôturée' ) {
                $sortie->setEtat($etats['Activité en cours']);
                $em->persist($sortie);
                continue;
            }
            $dateDebutPlusUnJour = (clone $sortie->getDateHeureDebut())->modify('+1 day');
            if ($dateDebutPlusUnJour <= $currentDate && $etatOrig == 'Activité en cours' ) {
                $sortie->setEtat($etats['Passée']);
                $em->persist($sortie);
                continue;
            }

            $dateDebutPlusUnMois = (clone $sortie->getDateHeureDebut())->modify('+1 month');
            if ($dateDebutPlusUnMois <= $currentDate && ($etatOrig == 'Passée' || $etatOrig == 'Annulée'))  {
                $sortie->setEtat($etats['Archivée']);
                $em->persist($sortie);
            }

        }
        $em->flush();

    }

}