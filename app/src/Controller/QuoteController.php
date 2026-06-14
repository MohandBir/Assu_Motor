<?php

namespace App\Controller;

use App\Entity\Formula;
use App\Form\QuoteType;
use App\Service\QuoteCalculator;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class QuoteController extends AbstractController 
{
    #[Route('/quote', name: 'app_quote')]
    public function index(Request $request, QuoteCalculator $quoteCalculator, EntityManagerInterface $em): Response
    {
        $session = $request->getSession();
        $quote = $session->get('quote');

        // Réattacher les entités liées à Doctrine
        if ($quote !== null) {
            if ($quote->getFormula() !== null) {
                $quote->setFormula(
                    $em->find(Formula::class, $quote->getFormula()->getId())
                );
            }
        }

        $form = $this->createForm(QuoteType::class, $quote);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $quote = $form->getData();
            $basePrice = $quote->getFormula()->getbasePrice();
            $quote->setBonusMalus(1);
            $quote->setStatus('pending');
            $quote->setCreatedAt(new DateTimeImmutable('now'));
            $quote->setExpiredAt(new DateTimeImmutable('+1 month'));
            
            // calcule de pix estimé
            $estimatedPrice = $quoteCalculator->getPrice($quote, $basePrice);
            $quote->setEstimatedPrice($estimatedPrice * $quote->getDuration());

            //Session
            $session->set('quote', $quote);

            return $this->redirectToRoute('app_quote_showResult');
        } 

        return $this->render('quote/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/quote/result', name: 'app_quote_showResult')]
    public function showResult(Request $request): Response
    {
        $session = $request->getSession();
        //dd($session->get('quote'));
        return $this->render('quote/result.html.twig', [
            'quote' => $session->get('quote'),
        ]);
    }

    #[Route('/quote/save', name: 'app_quote_save')]
    public function save(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $session = $request->getSession();

        if ($session->has('quote')) {
            $quote = $session->get('quote');
            //dd($quote);
            $quote->setUser($user);
    
            // Réattacher les entités liées à Doctrine
            $quote->setFormula(
                $em->find(Formula::class, $quote->getFormula()->getId())
                );   
    
            $em->persist($quote);
            $em->flush();
            $this->addFlash('success', 'Votre devis a bien été enregistré.');
            $session->clear();
    
            }
            
        return $this->redirectToRoute('app_home');
    }
}
