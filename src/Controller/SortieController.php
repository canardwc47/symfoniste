<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\AnnulationType;
use App\Form\LieuType;
use App\Form\Models\Recherche;
use App\Form\RechercheType;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use App\Repository\SortieRepository;
use App\Service\SortieService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use function Symfony\Component\Clock\now;

final class SortieController extends AbstractController
{
    #[Route('/', name: 'sortie_liste', methods: ['GET', 'POST'])]
    public function liste(
        SortieRepository $sortieRepository,
        Request $request,
        Security $security
    ): Response {
        $recherche = new Recherche();
        $rechercheForm = $this->createForm(RechercheType::class, $recherche);
        $rechercheForm->handleRequest($request);

        if ($rechercheForm->isSubmitted() ) {
            $sorties = $sortieRepository->rechercheSortie($recherche, $security);
        } else {
            $sorties = $sortieRepository->findAll();
        }

        return $this->render('sortie/liste.html.twig', [
            'sorties' => $sorties,
            'rechercheForm' => $rechercheForm->createView()
        ]);
    }

    #[Route('/sortie/{id}/inscrire', name: 'sortie_inscrire', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function inscrire(
        int                    $id,
        SortieService          $sortieService,
        Security               $security,
        EntityManagerInterface $em
    ): Response
    {
        $result = $sortieService->inscription($id, $security, $em);
        if ($result === "Inscription réussie.") {
            $this->addFlash('success', $result);
        } else {
            $this->addFlash('warning', $result);
        }
        return $this->redirectToRoute('sortie_liste');
    }

    #[Route('/sortie/{id}/desister', name: 'sortie_desister', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function desister(
        int                    $id,
        SortieService          $sortieService,
        Security               $security,
        EntityManagerInterface $em
    ): Response
    {
        $result = $sortieService->desistement($id, $security, $em);
        if ($result === "Désistement enregistré.") {
            $this->addFlash('success', $result);
        } else {
            $this->addFlash('warning', $result);
        }
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

    #[Route('/sortie/creer', name: 'sortie_creer', methods: ['GET', 'POST'])]
    public function create(
        Request                $request,
        EntityManagerInterface $em,
    ): Response
    {
        //Création de l'entité vide
        $sortie = new Sortie();
        $sortie->setOrganisateur($this->getUser());
        $sortie->addParticipant($sortie->getOrganisateur());
        $etat = $em->getRepository(Etat::class)->findOneBy(['libelle' => 'Créée']);
        $sortie->setEtat($etat);
        $sortie->setSite($sortie->getOrganisateur()->getSite());

        //Création du formulaire SORTIE et association de l'entité vide.
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {

            $em->persist($sortie);
            $em->flush();

            $this->addFlash('success', 'Ta sortie a bien été créée!');
            return $this->redirectToRoute('sortie_liste', ["sorties" => $sortie]);
        }

        return $this->render('sortie/creer.html.twig', [
            "sortieForm" => $sortieForm->createView()
        ]);
    }

    #[Route('/sortie/{id}/update', name: 'sortie_update', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    /*#[IsGranted ('SORTIE-EDIT', 'sortie')]*/
    public function update(Sortie $sortie, Request $request, EntityManagerInterface $em): Response
    {
        if (!$sortie) {
            throw $sortie->createNotFoundExecption('Cette sortie n\'existe pas désolée ');
        }

        $sortieForm = $this->createForm(SortieType::class, $sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $em->flush();
            $this->addFlash('success', 'La mise à jour de ta sortie a été effectuée avec succès!');

            return $this->redirectToRoute('sortie_liste', ['id' => $sortie->getId()]);
        }

        return $this->render('sortie/creer.html.twig', [
            "sortieForm" => $sortieForm,
            "sortie" => $sortie
        ]);
    }

    #[Route('/sortie/{id}/publier', name: 'sortie_publier', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function publish(
        int                    $id,
        SortieRepository       $sortieRepository,
        SortieService          $sortieService,
        EntityManagerInterface $em): Response
    {
        $sortie = $sortieRepository->find($id);
        if (!$sortie) {
            throw $this->createNotFoundException('Sortie non trouvée');
        }
        $sortieService->publierSortie($sortie, $em);

        $this->addFlash('success', 'Ta sortie a été publiée avec succès!');
        return $this->redirectToRoute('sortie_liste');
    }


    // SUPPRESSION D'UNE SORTIE a peaufiner et ajouter des trucs dans le twig pour que ca fonctionne aussi cf csrf token!!
    #[Route('/sortie/{id}/delete', name: 'sortie_delete', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function delete(
        int                    $id,
        SortieRepository       $sortieRepository,
        Request                $request,
        EntityManagerInterface $em): Response
    {
        $sortie = $sortieRepository->find($id);
        //s'il n'existe pas dans la bdd, on lance une erreur 404
        if (!$sortie) {
            throw $this->createNotFoundException('Cette sortie n\'existe pas désolé!');
        }

        //si je ne suis pas le proprio et que je ne suis pas admin alors je ne peux pas y accéder
//        if(!($sortie->getUser() === $this->getUser() || $this->isGranted("ROLE_ADMIN"))){
//            throw $this->createAccessDeniedException("Tu n'as pas le droit de faire ça désolé !");
//        }
        /*TODO:faire les accès avec les roles */
        /*        if (!$this->isGranted('SORTIE_DELETE', $sortie)) {
                    throw $this->createAccessDeniedException("Malheureusement, tu ne peux pas utiliser cette modalité.");
                }*/
        /* if ($this->isCsrfTokenValid('delete' . $sortie->getId(), $request->query->get('_token'))) {*/
        $em->remove($sortie, true);
        $em->flush();
        $this->addFlash('success', 'Ta sortie a bien été supprimée !');
        /*       } else {
                   $this->addFlash('danger', 'Ta sortie ne peut pas être supprimée !');
               }*/
        return $this->redirectToRoute('sortie_delete');

    }

    #[Route('/sortie/{id}/annulation', name: 'sortie_annulation', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function annulation(

        int                    $id,
        SiteRepository         $siteRepository,
        SortieRepository       $sortieRepository,
        EtatRepository         $etatRepository,
        Request                $request,
        EntityManagerInterface $em): Response
    {

        $sortie = $sortieRepository->find($id);
        $site = $siteRepository->find($sortie->getSite()->getId());

        $etatAnnule = $etatRepository->findBy(['libelle' => 'Annulée']);



        $annulationForm = $this->createForm(AnnulationType::class, $sortie);
        $annulationForm->handleRequest($request);

        if ($annulationForm->isSubmitted() && $annulationForm->isValid()) {
            $currentDate = new \DateTimeImmutable();
            if ($sortie->getDateHeureDebut() < $currentDate) {
                $this->addFlash('warning', 'Ta sortie est déjà passée !');
                return $this->redirectToRoute('sortie_liste');
            }

            $sortie->setEtat($etatAnnule[0]);
            $em->persist($sortie);
            $em->flush();
            $this->addFlash('success', 'Ta sortie a bien été annulée !');


            return $this->redirectToRoute('sortie_liste', ['id' => $sortie->getId(), 'siteId' => $site->getId()]);

        }

        return $this->render('sortie/annulation.html.twig', ["annulationForm" => $annulationForm, 'siteId' => $site->getId(), 'sortie' => $sortie]);

    }

}