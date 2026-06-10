<?php

namespace App\Controller;

use App\Form\QuoteType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class QuoteController extends AbstractController 
{
    #[Route('/quote', name: 'app_quote')]
    public function index(Request $request): Response
    {

        $form = $this->createForm(QuoteType::class);
        $form->handleRequest($request);
        //  dd('valid');
        if ($form->isSubmitted() ) {

            if ($form->isValid()) {
                
                }
        } 


        return $this->render('quote/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
