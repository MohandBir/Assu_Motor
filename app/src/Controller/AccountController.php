<?php

namespace App\Controller;

use App\Repository\QuoteRepository;
use App\Repository\SubscriptionRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AccountController extends AbstractController
{
    #[Route('/account/user', name: 'app_account_user')]
    public function user(UserRepository $user): Response
    {    
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('account/profile.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/account/quote', name: 'app_account_quote')]
    public function quote(QuoteRepository $quoteRepo): Response
    {    
        if (!$this->getUser()) {
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
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        $subscriptions = $subscriptionRepo->findAllOrdredBySubmittedAt($this->getUser());
        //dd($subscriptions[0]->getTranslatedStatus());
        return $this->render('account/subscription.html.twig', [
            'subscriptions' => $subscriptions,
        ]);
    }
}
