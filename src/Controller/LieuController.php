<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuType;
use App\Repository\LieuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/lieu', name: 'lieu_')]
final class LieuController extends AbstractController
{
    #[Route(name: 'index', methods: ['GET'])]
    public function index(LieuRepository $lieuRepository): Response
    {
        $lieux = $lieuRepository->createQueryBuilder('l')
            ->join('l.ville', 'v')
            ->orderBy('v.nom', 'ASC')
            ->addOrderBy('l.nomLieu', 'ASC')
            ->getQuery()
            ->getResult();
        return $this->render('lieu/index.html.twig', [
            'lieux' => $lieux,
        ]);
    }

    #[Route('/lieu/ajouterLieu', name: 'ajouter_Lieu', methods: ['GET', 'POST'])]
    public function ajouterLieu(
        Request                $request,
        EntityManagerInterface $entityManager,
    ) : Response
    {
        $lieu = new Lieu();
        $formLieu = $this->createForm(LieuType::class, $lieu);
        $formLieu->handleRequest($request);

        if ($formLieu->isSubmitted() && $formLieu->isValid()) {
            $entityManager->persist($lieu);
            $entityManager->flush();

            $this->addFlash('success', 'Ton lieu a bien été ajouté!');
            return $this->redirectToRoute('lieu_index');
        }

        return $this->render('lieu/ajouterLieu.html.twig', [
            "formLieu" => $formLieu->createView()
        ]);

    }

}


