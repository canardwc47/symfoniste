<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
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

    #[Route('/test-403', name: 'test_403')]
    public function test403(): Response
    {
       return $this->render('@Twig/Exception/error403.html.twig');
    }

    #[Route('/test-404', name: 'test_404')]
    public function test404(): Response
    {
        return $this->render('@Twig/Exception/error404.html.twig');
    }

    #[Route('/test-500', name: 'test_500')]
    public function test500(): Response
    {
        return $this->render('@Twig/Exception/error500.html.twig');
    }

    #[Route('/test-503', name: 'test_503')]
    public function test503(): Response
    {
        return $this->render('@Twig/Exception/error503.html.twig');
    }

    #[Route('/test-401', name: 'test_401')]
    public function test401(): Response
    {
        return $this->render('@Twig/Exception/error401.html.twig');
    }

}
