<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MainController extends AbstractController
{
    #[Route('/mentions-legales', name: 'main_mentions')]
    public function mention(): Response
    {
        return $this->render('main/mentionLegales.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
    #[Route('politique-confidentialite', name: 'main_pConfidentialite')]
    public function P_confidentialite(): Response
    {
        return $this->render('main/pConfidentialite.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
}
