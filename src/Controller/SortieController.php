<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\EtatRepository;

use App\Repository\ParticipantRepository;

use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SortieController extends AbstractController
{
    #[Route('/sortie', name: 'sortie_liste', methods: ['GET'])]
    public function liste(SortieRepository $sortieRepository): Response
    {
        $sorties = $sortieRepository->findAll();
        return $this->render('sortie/liste.html.twig', [
            'sorties' => $sorties,
        ]);
    }

    #[Route('/sortie', name: 'sortie_inscrire', methods: ['GET', 'POST'])]
    public function inscrire(
        SortieRepository $sortieRepository,
        ParticipantRepository $participantRepository,
        EntityManagerInterface $em
    ): Response
    {
        $sortie = new Sortie();
        $sortie->addParticipant($participantRepository->find(21));

        $em->persist($sortie);
        $em->flush();

        $sorties = $sortieRepository->findAll();


        return $this->render('sortie/liste.html.twig', [
            'sorties' => $sorties,
        ]);
    }

    #[Route('/sortie/create', name: 'sortie_create', methods: ['GET', 'POST'])]
    public function create(
        Request                $request,
        EntityManagerInterface $em,
        SortieRepository $sortieRepository,
        EtatRepository $etatRepository

    ): Response
    {
        $sorties = $sortieRepository->findAll();
        //Création de l'entité vide
        $sortie = new Sortie();
        $sortie->setOrganisateur(null);
        $etat = $etatRepository->find(1);
        $sortie->setEtat($etat);


        //Création du formulaire et association de l'entité vide.

        $sortieForm = $this->createForm(SortieType::class, $sortie);
        //Récupère les données du formulaire et on les injecte dans notre $wish.
        $sortieForm->handleRequest($request);
        //On vérifie si le formulaire a été soumis et que les données soumises sont valides.
        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            //Hydrater les propriétés absentes du formulaire
//            $wish->setIsPublished(true);
            //Sauvegarde dans la Bdd
            //ajout de la relation avec le user
            //$sortie->setUser($this->getUser());

            $em->persist($sortie);
            $em->flush();

            //Affiche un message à l'utilisateur sur la prochaine page.
            $this->addFlash('success', 'Ta sortie a bien été créée!');

            //Redirige vers la page de detail du wish
           // return $this->redirectToRoute('sortie.html.twig');
            return $this->render('sortie/liste.html.twig', [
                'sorties' => $sorties,
            ]);
        }
        //Affiche le formulaire
        return $this->render('sortie/create.html.twig', ["sortieForm" => $sortieForm]);
    }

}