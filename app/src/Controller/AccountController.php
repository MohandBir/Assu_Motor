<?php

namespace App\Controller;

use App\Repository\QuoteRepository;
use App\Repository\SubscriptionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AccountController extends AbstractController
{
    #[Route('/account/quote', name: 'app_account_quote')]
    public function quote(QuoteRepository $quoteRepo): Response
    {    
        if (!$this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('app_login');
        }
        $quotes = $quoteRepo->findWithVehicleAndFormula($this->getUser());

        return $this->render('account/quote.html.twig', [
            'quotes' => $quotes,
        ]);
    }

    #[Route('/account/subscription', name: 'app_account_subscription')]
    public function subscription(SubscriptionRepository $subscriptionRepo): Response
    {    
        if (!$this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('app_login');
        }
        $subscriptions = $subscriptionRepo->findAll();

        return $this->render('account/quote.html.twig', [
            'subscriptions' => $subscriptions,
        ]);
    }
}
