<?php

namespace App\Controller;

use App\Form\QuoteType;
use App\Service\QuoteCalculator;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class QuoteController extends AbstractController 
{
    #[Route('/quote', name: 'app_quote')]
    public function index(Request $request, QuoteCalculator $quotecalculator): Response
    {

        $form = $this->createForm(QuoteType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $quote = $form->getData();
            $basePrice = $quote->getFormula()->getbasePrice();
            $quote->setBonusMalus(1);
            $quote->setStatus('pending');
            $quote->setCreatedAt(new DateTimeImmutable('now'));
            $quote->setExpiredAt(new DateTimeImmutable('+1 month'));

            $estimatedPrice = $quotecalculator->getPrice($quote, $basePrice);
            $quote->setEstimatedPrice($estimatedPrice);

            return $this->render('quote/price.html.twig', [
                'quote' => $quote,
                'estimatedPrice' => $estimatedPrice,
            ]);
        } 

        return $this->render('quote/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/quote/estimated_Price', name: 'app_quote_showPrice')]
    public function showPrice(): Response
    {
        return $this->render('quote/price.html.twig', [
            
        ]);
    }
}
