<?php

namespace App\Controller;

use App\Entity\Quote;
use App\Form\QuoteType;
use App\Repository\BrandRepository;
use App\Repository\ModelRepository;
use App\Repository\VehicleReferenceRepository;
use App\Service\QuoteCalculator;
use App\Service\QuoteManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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

    #[Route('/quote/{id}', name: 'app_quote_new',  requirements: ['id' => '\d+'], defaults: ['id' => null])]
    public function new(?Quote $savedQuote ,Request $request): Response
    {
        $user = $this->getUser();
        // contrôle d'accès
        if ($savedQuote) {
            if ($user !== $savedQuote->getUser()) {
                return $this->redirectToRoute('app_login');    
            }
        }
        //réccupere la session
        $session = $request->getSession();
        // initiation d'objet quote
        if ($user) {
            $quote = $savedQuote ?? null;
        } else {
            $quote = $session->get('quote');
            // Réattacher les entités liées à Doctrine
            $this->quoteManager->reatacheFormula($quote);
        }
        
        $form = $this->createForm(QuoteType::class, $quote);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // charger les données soumises
            $quote = $form->getData();
            // verifier la combinaison
            if (!$this->quoteManager->isValidVehicle($quote)) {
                $this->addFlash('danger', 'Combinaison marque/modèle/année invalide.');
                
                return $this->redirectToRoute('app_quote_new');
            }
            // complétet l'objet quote
            $quote = $this->quoteManager->completeFormDataQuote($quote);
            // calculer le prix estimé
            $estimatedPrice = $this->calculator->getPrice($quote);
            $quote->setEstimatedPrice(round($estimatedPrice * $quote->getDuration(), 2));
            // sauvegarde en BDD ou en session 
            if($user) {
                $message = $savedQuote ? 'modifié' : 'sauvegardé'; 
                $quote->setUser($user);
                $this->em->persist($quote);
                $this->addFlash('success', "Votre devis a bien été $message" );
                $this->em->flush();
            } else {
                $session->set('quote', $quote);
            }
            // redirection vers la route resultat
            return $this->redirectToRoute('app_quote_showResult', [
                'id' => $user ? $quote->getId() : null ,
            ]);
        } 

        return $this->render('quote/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/quote/result/{id}', name: 'app_quote_showResult', requirements: ['id' => '\d+'], defaults: ['id' => null])]
    public function showResult(?Quote $savedQuote, Request $request): Response
    {
        $session = $request->getSession();
        $quote = $session->get('quote');

        return $this->render('quote/result.html.twig', [
            'quote' => $this->getUser() ? $savedQuote : $quote,
        ]);
    }
    
    #[Route('/quote/delete/{id}', name: 'app_quote_delete', requirements: ['id' => '\d+'], defaults: ['id' => null])]
    public function delete(?Quote $quote, Request $request): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');    
        }

        $submittedToken = $request->getPayload()->get('token'); 

        if ($quote && $this->isCsrfTokenValid('delete-quote' . $quote->getId(), $submittedToken)) {
            $this->em->remove($quote);
            $this->em->flush();
            $this->addFlash('success', 'Le devis a été supprimé avec succès.');

            return $this->redirectToRoute('app_account_quote');
        } else {
            $this->addFlash('danger', 'Opération non autorisée ');

            return $this->redirectToRoute('app_home');
        }
    }
    
    #[Route('/quote/brand/{brandName}', name: 'app_quote_getModelAjax')]
    public function getModelAjax(string $brandName, ModelRepository $modelRepo, BrandRepository $brandRepo): JsonResponse
    {   
        $brand = $brandRepo->findOneBy(['name' => $brandName]);
        $objectsModels = $modelRepo->findBy(['brand' => $brand]);
        $models = [];
        foreach ($objectsModels as $model) {
            $models[] = $model->getName();
        }
        
        return new JsonResponse($models);
    }
    
    #[Route('/quote/model/{modelName}', name: 'app_quote_getYearAjax')]
    public function getYearAjax(string $modelName, VehicleReferenceRepository $referenceRepo, ModelRepository $modelRepo): JsonResponse
    {   
        $model = $modelRepo->findOneBy(['name' => $modelName]);
        $objectsReferences = $referenceRepo->findBy(['model' => $model]);
        $years = [];
        foreach ($objectsReferences as $reference) {
            $years[] = $reference->getYear();
        }
        
        return new JsonResponse($years);
    }
}
