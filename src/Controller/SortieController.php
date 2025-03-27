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

        $participant = $this->getUser();
        $sorties = $sortieRepository->findAll();
        $sortiesOrganisateur = $sortieRepository->findByOrganisateur($participant);
        return $this->render('sortie/liste.html.twig', [
            'sorties' => $sorties,
            'sortiesOrganisateur' => $sortiesOrganisateur
        ]);
    }

    #[Route('/sortie', name: 'sortie_inscrire', methods: ['GET', 'POST'])]
    public function inscrire(int $id, SortieRepository $sortieRepository, EntityManagerInterface $em): Response
    {
        $participant = $this->getUser(); // Récupère l'utilisateur connecté (qui est un Participant)

        // 1️⃣ Récupère la sortie existante en base de données grâce à son ID
        $sortie = $sortieRepository->find($id);

        // 2️⃣ Vérifie que la sortie existe
        if (!$sortie) {
            throw $this->createNotFoundException("Cette sortie n'existe pas.");
        }

        // 3️⃣ Ajoute l'utilisateur connecté à la liste des participants
        $sortie->addParticipant($participant);

        // 4️⃣ Sauvegarde en base de données
        $em->persist($sortie);
        $em->flush();

        return $this->redirectToRoute('sortie_liste'); // Redirection après inscription
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
        $sortie->setOrganisateur($this->getUser());
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

    #[Route('/sortie/{id}/update', name: 'sortie_update', requirements: ['id' => '\d+'], methods: ['GET','POST'])]
    /*#[IsGranted ('SORTIE-EDIT', 'sortie')]*/
    public function update (Sortie $sortie, Request $request, EntityManagerInterface $em): Response
        {
            if (!$sortie){
                throw $sortie-> createNotFoundExecption('Cette sortie n\'existe pas désolée ');
            }

            $sortieForm = $this->createForm(SortieType::class, $sortie);
            //Récupère les données du formulaire et on les injecte dans notre $sortie.
            $sortieForm->handleRequest($request);

            if ($sortieForm->isSubmitted() && $sortieForm->isValid()){

                $em->flush();
                $this->addFlash('success', 'La mise à jour de ta sortie a été effectuée avec succès!');

                return $this->redirectToRoute('sortie_liste', ['id'=> $sortie->getId()]);
            }
        return $this->render('sortie/create.html.twig', ["sortieForm"=> $sortieForm]);
}

        // SUPPRESSION D'UNE SORTIE  !!
    #[Route('/sortie/{id}/delete', name: 'sortie_delete', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function delete(int $id, SortieRepository $sortieRepository, Request $request, EntityManagerInterface $em): Response
    {
        $sortie = $sortieRepository->find($id);
        //s'il n'existe pas dans la bdd, on lance une erreur 404
        if (!$sortie) {
            throw $this->createNotFoundException('Cette sortie n\'existe pas désolé!');
        }

        //si je ne suis pas le proprio et que je ne suis pas admin alors je ne peux pas y accéder
//        if(!($wish->getUser() === $this->getUser() || $this->isGranted("ROLE_ADMIN"))){
//            throw $this->createAccessDeniedException("Pas possible gamin !");
//        }
        /*TODO:faire les accès avec les roles */
/*        if (!$this->isGranted('SORTIE_DELETE', $sortie)) {
            throw $this->createAccessDeniedException("Malheureusement, tu ne peux pas utiliser cette modalité.");
        }*/
        if ($this->isCsrfTokenValid('delete' . $sortie->getId(), $request->query->get('_token'))) {
            $em->remove($sortie, true);
            $em->flush();
            $this->addFlash('success', 'Ta sortie a bien été supprimée !');
        } else {
            $this->addFlash('danger', 'Ta sortie ne peut pas être supprimée !');
        }
        return $this->redirectToRoute('sortie_liste');
   }

}