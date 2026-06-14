<?php

namespace App\Controller;

use App\Repository\QuoteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AccountController extends AbstractController
{
    #[Route('/account/quote', name: 'app_account_quote')]
    public function quote(QuoteRepository $quoteRepo): Response
    {    
        if (!$this->isGranted('ROLE_USER')) {
            dd('hi');
        }
        $quotes = $quoteRepo->findWithVehicleAndFormula($this->getUser());

        return $this->render('account/quote.html.twig', [
            'quotes' => $quotes,
        ]);
    }
}
