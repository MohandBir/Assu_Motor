<?php

namespace App\Controller\Admin;

use App\Entity\Document;
use App\Entity\Subscription;
use App\Repository\SubscriptionRepository;
use App\Service\DocumentManager;
use App\Service\SubscriptionManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

final class AdminSubscriptionController extends AbstractController
{
    public function __construct(
        private  SubscriptionRepository $subscriptionRepo,
        private EntityManagerInterface $em,
        private DocumentManager $documentManager,
    )
    {}
    #[Route('/admin', name: 'app_admin_dashboard')]
    public function dashboard(): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', 'accès non autorisé, veuillez vous connecter au compte coordonnée appropriés');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('admin/dashboard.html.twig', [
        
        ]);
    }

    #[Route('/admin/subscription/index', name: 'app_admin_subscription_index')]
    public function index(): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', 'accès non autorisé, veuillez vous connecter au compte approprié');

            return $this->redirectToRoute('app_login');
        }
        $subscriptions = $this->subscriptionRepo->findWidthQuoteVehicleFormulaDocument();
        //dd($subscriptions);

        return $this->render('admin/subscription/index.html.twig', [
            'subscriptions' => $subscriptions,
        ]);
    }

    #[Route('/admin/subscription/show/{id}', name: 'app_admin_subscription_show', requirements: ['id' => '\d+'], defaults: ['id' => null])]
    public function show(?Subscription $subscription): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', 'accès non autorisé, veuillez vous connecter au compte approprié');

            return $this->redirectToRoute('app_login');
        }
        if($subscription) {
            $subscription = $this->subscriptionRepo->findOneWidthQuoteVehicleFormulaDocument($subscription->getId());
        } else {
            $this->addFlash('warning', '404 page introuvable');

            return $this->redirectToRoute('app_admin_subscription_index');
        }
        //dd($subscriptions);

        return $this->render('admin/subscription/show.html.twig', [
            'subscription' => $subscription,
            'quote' => $subscription->getQuote(),
            'documents' => $subscription->getDocuments(),
        ]);
    }

    #[Route('/admin/subscription/change/status/{id}/{statusTarget}', name: 'app_admin_subscription_changeStatus', requirements: ['id' => '\d+'], defaults: ['id' => null])]
    public function changeStatus(?Subscription $subscription, string $statusTarget, SubscriptionManager $subscriptionManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', 'accès non autorisé, veuillez vous connecter au compte approprié');

            return $this->redirectToRoute('app_login');
        }

        if (!$subscription) {
            $this->addFlash('warning', '404 page introuvable');

            return $this->redirectToRoute('app_admin_subscription');
        }

        $flashMessage = $subscriptionManager->changeStatus($subscription, $statusTarget);

        if (!$flashMessage) {
            $this->addFlash('warning', '404 page introuvable');
        } elseif ($flashMessage === 'isAlreadyChanged') {
            $this->addFlash('info', 'Le statut est déjà actualisé');
        } else {
            if ($statusTarget === Subscription::DOCUMENTS_INVALID) {
                $this->documentManager->removeFiles($subscription->getDocuments());
            }
            $this->addFlash('success', $flashMessage);
            $this->em->flush();
        }

        return $this->redirectToRoute('app_admin_subscription_show', [
            'id' => $subscription->getId(),
        ]);
    }

    #[Route('/admin/subscription/delete/{id}}', name: 'app_admin_subscription_delete')]
    public function delete(?Subscription $subscription, Request $request): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', 'accès non autorisé, veuillez vous connecter au compte approprié');

            return $this->redirectToRoute('app_login');
        }

        if (!$subscription) {
            $this->addFlash('warning', '404 page introuvable');

            return $this->redirectToRoute('app_admin_subscription');
        }

        $submittedToken = $request->getPayload()->get('token');
        if ($this->isCsrfTokenValid('delete-subscription'. $subscription->getId(), $submittedToken)) {

            $this->documentManager->removeFiles($subscription->getDocuments());
            $this->em->remove($subscription);
            $this->em->flush();

            $this->addFlash('success', 'La demande a été supprimée avec succès');

            return $this->redirectToRoute('app_admin_subscription_index');
        } else {
            $this->addFlash('danger', 'Accès non autorisée');

            return $this->redirectToRoute('app_logout');
        }
    }

    #[Route('/admin/subscription/doc/view/{id}', name: 'app_admin_subscription_doc_view')]
    public function view(?Document $document): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', 'accès non autorisé, veuillez vous connecter au compte approprié');

            return $this->redirectToRoute('app_login');
        }
        if ($document) {
            $filePath = $this->documentManager->getDocumentPath($document);

            if (!file_exists($filePath)) {
                $this->addFlash('warning', 'document non trouvé');

                return $this->redirectToRoute('app_admin_subscription_show', [
                    'id' => $document->getSubscription()->getId(),
                 ]);
            }

            $response = new BinaryFileResponse($filePath);
            $response->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_INLINE,
                $document->getName()
            );

            return $response;
        } 

        return new Response();   
    }
}
