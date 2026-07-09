<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {

        return $this->render('home/index.html.twig', []);
    }



    #[Route('/mentions-legales', name: 'app_home_mentions-legales')]
    public function mentionLegales(): Response
    {

        return $this->render('home/mention-legales.html.twig', []);
    }
}