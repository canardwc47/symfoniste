<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\LieuType;
use App\Form\SortieType;
use App\Repository\EtatRepository;

use App\Repository\ParticipantRepository;

use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;
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

    #[Route('/sortie/{id}/inscrire', name: 'sortie_inscrire', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function inscrire(
        int $id,
        SortieRepository $sortieRepository,
        Security $security,
        ParticipantRepository $participantRepository,
        EntityManagerInterface $em
    ): Response
    {
        $user = $security->getUser();
        $participant = $participantRepository->findOneBy(['id' => $user]);

        $sortie = $sortieRepository->find($id);
        $sortie->addParticipant($participant);

        $em->persist($sortie);
        $em->flush();

        return $this->redirectToRoute('sortie_liste');
    }


     #[Route('/sortie/{id}/desister', name:'sortie_desister', requirements: ['id' => '\d+'], methods: ['GET'])]

    public function desister(
         int $id,
         SortieRepository $sortieRepository,
         ParticipantRepository $participantRepository,
         Security $security,
         EntityManagerInterface $em
    ): Response
    {
        $user = $security->getUser();
        $participant = $participantRepository->findOneBy(['id' => $user]);

        $sortie = $sortieRepository->find($id);
        $sortie->removeParticipant($participant);


        // 4️⃣ Sauvegarde en base de données
        $em->persist($sortie);
        $em->flush();

        return $this->redirectToRoute('sortie_liste');
    }


    #[Route('/sortie/{id}/detail', name: 'sortie_detail', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function detail(int $id, SortieRepository $sortieRepository): Response
        /*public function detail(Sortie $sortie, SortieRepository $sortieRepository): Response*/
    {
        //Récupère la sortie en fonction de l'id présent dans l'url
        $sortie = $sortieRepository->find($id);
        if (!$sortie) {
            throw $this->createNotFoundException('Cette sortie n\'existe pas désolé!');
        }
        return $this->render('sortie/detail.html.twig', ["sortie" => $sortie]);
    }

    #[Route('/sortie/create', name: 'sortie_create', methods: ['GET', 'POST'])]
    public function create(
        Request                $request,
        EntityManagerInterface $em,
    ): Response
    {
        //Création de l'entité vide
        $sortie = new Sortie();
        $sortie->setOrganisateur($this->getUser());
        $sortie->addParticipant($sortie->getOrganisateur());
        $etat = $em->getRepository(Etat::class)->find(1);
        $sortie->setEtat($etat);
        $sortie->setSite($this->getUser()->getSite());

        //Création du formulaire LIEU
        $lieuForm = $this->createForm(LieuType::class);
        $lieuForm->handleRequest($request);
        if ($lieuForm->isSubmitted() && $lieuForm->isValid()) {
            $lieu = $lieuForm->getData();
            $lieu->setVille($lieu->getVille());
            $lieu->setNomLieu($lieu->getNomLieu());
            $lieu->setRue($lieu->getRue());
            $lieu->setLatitude($lieu->getLatitude());
            $lieu->setLongitude($lieu->getLongitude());
            $sortie->setLieu($lieu);
        }

        //Création du formulaire SORTIE et association de l'entité vide.
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {

            $em->persist($sortie);
            $em->flush();

            $this->addFlash('success', 'Ta sortie a bien été créée!');

            return $this->redirectToRoute('sortie_liste');

        }
        //Affiche le formulaire
        return $this->render('sortie/create.html.twig', [
            "lieuForm" => $lieuForm->createView(),
            "sortieForm" => $sortieForm->createView()
        ]);
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


        // SUPPRESSION D'UNE SORTIE a peaufiner et ajouter des trucs dans le twig pour que ca fonctionne aussi!!
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