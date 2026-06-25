<?php

namespace App\Controller;

use App\Entity\Quote;
use App\Entity\Subscription;
use App\Form\SubscriptionType;
use App\Repository\SubscriptionRepository;
use App\Service\DocumentManager;
use App\Service\SubscriptionManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SubscriptionController extends AbstractController
{
    public function __construct(
        private SubscriptionManager $subscriptionManager,
        private DocumentManager $documentManager,
        private EntityManagerInterface $em,
    ) {}    

    #[Route('/subscription/new/{id}', name: 'app_subscription_new')]
    public function new(?Quote $quote, Request $request): Response
    {
        //middlware
        if (!$quote) {
            $this->addFlash('warning', '404 Page introuvable');
            return$this->redirectToRoute('app_home');    
        } elseif(!$this->getUser() || $quote->getUser() !== $this->getUser()) {
            $this->addFlash('danger', 'Accès non autorisé');
            return$this->redirectToRoute('app_logout'); 
        }
        
        $form = $this->createForm(SubscriptionType::class, null, [
            'validation_groups' => function(FormInterface $form) {
                if ($form->get('submitDocs')->isClicked()) {
                    return ['Default', 'validate_docs'];
                }
                return ['Default'];
            }
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) { 

            $subscription = $this->subscriptionManager->completeSubscription($quote);
            $quote->setStatus(Quote::SUBSCRIBED);

            $this->em->persist($subscription);
            
            if ($form->get('submitDocs')->isClicked()) {
                $files = [
                    'licenceFile' => $form->get('drivingLicense')->getData(),
                    'grayCardFile' => $form->get('grayCard')->getData()
                ];
                $subscription = $this->documentManager->handleDocument($files, $subscription);
                $this->addFlash('success', 'Votre demande a été bien crée, complété et transmise à l\'étude');               
            } else {
                $this->addFlash('info', 'Votre demande est crée , pour qu\'elle soit traitée ,veuillez charger vos documents');
            }       
            $this->em->flush();
                    
            return $this->redirectToRoute('app_account_subscription');
        }

        return $this->render('subscription/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/subscription/add/doc/{id}', name: 'app_subscription_add_doc')]
    public function addDocuments(?Subscription $subscription, Request $request): Response
    {
        //middlware
        if (!$subscription) {
            $this->addFlash('warning', '404 Page introuvable');
            return$this->redirectToRoute('app_home');    
        } elseif(!$this->getUser() || $subscription->getUser() !== $this->getUser()) {
            $this->addFlash('danger', 'Accès non autorisé');
            return$this->redirectToRoute('app_logout'); 
        }

        $form = $this->createForm(SubscriptionType::class, $subscription, [
            'validation_groups' => function(FormInterface $form) {
                if ($form->get('submitDocs')->isClicked()) {
                    return ['Default', 'validate_docs'];
                }
                return ['Default'];
            }
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) { 
           
            if ($form->get('submitDocs')->isClicked()) {
                $files = [
                    'licenceFile' => $form->get('drivingLicense')->getData(),
                    'grayCardFile' => $form->get('grayCard')->getData()
                ];
                $subscription = $this->documentManager->handleDocument($files, $subscription);
                $this->addFlash('success', 'Vos documents ont bien été transmis, votre demande est prête à l\'étude');               
            } else {
                $this->addFlash('info', 'Votre demande est toujours sauvegardée, pensez à compléter vos documents très prochainement');
            }       
            $this->em->flush();
                    
            return $this->redirectToRoute('app_account_subscription');
        }

        return $this->render('subscription/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/subscription/show/{id}', name: 'app_subscription_show')]
    public function show(?Subscription $subscription, SubscriptionRepository $subscriptionRepo, Request $request): Response
    {
        //middlware
        if (!$subscription) {
            $this->addFlash('warning', '404 Page introuvable');
            return$this->redirectToRoute('app_home');    
        } elseif(!$this->getUser() || $subscription->getUser() !== $this->getUser()) {
            $this->addFlash('danger', '404 Page introuvable');
            return$this->redirectToRoute('app_logout'); 
        }  
        $subscription = $subscriptionRepo->findOneWidthQuoteVehicleFormulaDocument($subscription->getId());

        return $this->render('subscription/show.html.twig', [
            'subscription' => $subscription,
            'quote' => $subscription->getQuote(),
            'documents' => $subscription->getDocuments(),

        ]);
    }

    #[Route('/subscription/cancel/{id}', name: 'app_subscription_cancel', requirements: ['id' => '\d+'], defaults: ['id' => null])]
    public function delete(?Subscription $subscription, Request $request): Response
    {
        //middlware
        if (!$subscription) {
            $this->addFlash('warning', '404 Page introuvable');
            return$this->redirectToRoute('app_home');    
        } elseif(!$this->getUser() || $subscription->getUser() !== $this->getUser()) {
            $this->addFlash('danger', '404 Page introuvable');
            return$this->redirectToRoute('app_logout'); 
        }

        $submittedToken = $request->getPayload()->get('token'); 
        if ($subscription && $this->isCsrfTokenValid('cancel-subscription' . $subscription->getId(), $submittedToken)) {
            $subscription->setStatus(Subscription::CANCELLED);
            
            $this->em->flush();
            $this->addFlash('info', 'La demande de souscription a été annulée avec succès.');

            return $this->redirectToRoute('app_account_subscription');
        } else {
            $this->addFlash('danger', 'Opération non autorisée ');

            return $this->redirectToRoute('app_home');
        }
    }
}
