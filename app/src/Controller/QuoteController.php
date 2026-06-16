<?php

namespace App\Controller;

use App\Entity\Formula;
use App\Entity\Quote;
use App\Form\QuoteType;
use App\Repository\QuoteRepository;
use App\Repository\VehicleRepository;
use App\Service\QuoteCalculator;
use App\Service\QuoteManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class QuoteController extends AbstractController 
{
    public function __construct(
        private QuoteCalculator $calculator,
        private QuoteManager $quoteManager,
        private EntityManagerInterface $em,
        ) {}

    #[Route('/quote/{id}', name: 'app_quote',  requirements: ['id' => '\d+'], defaults: ['id' => null])]
    public function index(?Quote $savedQuote ,Request $request): Response
    {
        $session = $request->getSession();
        $user = $this->getUser();
        if ($user) {
            $quote = $savedQuote ?? null;
        } else {
            dd('dd');
            $quote = $session->get('quote');
            // Réattacher les entités liées à Doctrine
            $this->quoteManager->reatacheFormula($quote);
        }
        
        $form = $this->createForm(QuoteType::class, $quote);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $quote = $form->getData();
            $quote = $this->quoteManager->completeFormDataQuote($quote);
            $estimatedPrice = $this->calculator->getPrice($quote);
            $quote->setEstimatedPrice($estimatedPrice * $quote->getDuration());

            if($user) {
                $quote->setUser($user);
                $this->em->persist($quote);
                $this->addFlash('success', 'Votre devis a bien été enregistré');
                $this->em->flush();
            } else {
                $session->set('quote', $quote);
            }

            return $this->redirectToRoute('app_quote_showResult', [
                'id' => $user ? $quote->getId() : null ,
                //'id' => ($savedQuote) ? $savedQuote->getId() : null
            ]);
        } 

        return $this->render('quote/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/quote/result/{id}', name: 'app_quote_showResult', requirements: ['id' => '\d+'], defaults: ['id' => null])]
    public function showResult(?Quote $savedQuote, Request $request): Response
    {
        if ($this->getUser()) {
        
        }
        $session = $request->getSession();
        $quote = $session->get('quote');

        return $this->render('quote/result.html.twig', [
            'quote' => $this->getUser() ? $savedQuote : $quote,
            'isUpdated' => $savedQuote ? true : false,
        ]);
    }

    #[Route('/quote/save/{id}', name: 'app_quote_save', requirements: ['id' => '\d+'], defaults: ['id' => null])]
    public function save(?Quote $savedQuote, Request $request, VehicleRepository $vehicleRepository): Response
    {
        $user = $this->getUser();
        $session = $request->getSession();                     
        $quote = $session->get('quote');

        if ($savedQuote) {
            $vehicle = $vehicleRepository->find(($quote->getVehicle()->getId()));
            //dd($quote);
            $savedQuote = $this->quoteManager->reatacheFormula($quote);
            $savedQuote = $this->quoteManager->rebuildFromSession($savedQuote, $quote, $vehicle, $user);
            $this->em->persist($savedQuote);
            $this->addFlash('success', 'Votre devis a bien été modifié.');
           // dd($quote);
        } else {
            $quote->setUser($user);
            $quote = $this->quoteManager->reatacheFormula($quote);
            $this->em->persist($quote);
            $this->addFlash('success', 'Votre devis a bien été enregistré.');
           // dd($quote);
        }

        //dd($savedQuote);

        $this->em->flush();
        $session->clear();
   
        return $this->redirectToRoute('app_account_quote');
    }
    
    #[Route('/quote/delete/{id}', name: 'app_quote_delete', requirements: ['id' => '\d+'], defaults: ['id' => null])]
    public function delete(?Quote $savedQuote, Request $request): Response
    {
        if ($this->getUser() && $savedQuote) {
            $this->em->remove($savedQuote);
            $this->em->flush();
        }
        
        return $this->redirectToRoute('app_account_quote', [

        ]);
    }
}
