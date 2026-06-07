<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class QuoteController extends AbstractController
{
    #[Route('/quote', name: 'app_quote')]
    public function index(): Response
    {
        return $this->render('quote/index.html.twig', [
            
        ]);
    }
}
