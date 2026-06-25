<?php

namespace App\Controller\Admin;

use App\Entity\Subscription;
use App\Repository\SubscriptionRepository;
use App\Service\SubscriptionManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\EventListener\AddRequestFormatsListener;
use Symfony\Component\Routing\Attribute\Route;

final class AdminSubscriptionController extends AbstractController
{
    public function __construct(
        private  SubscriptionRepository $subscriptionRepo,
        private EntityManagerInterface $em,
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

        if($subscription) {
            $flashMessage = $subscriptionManager->changeStatus($subscription, $statusTarget);

            if($flashMessage !== 'isChecked') {
                $this->addFlash('success', $flashMessage);
                $this->em->flush();
            } elseif ($flashMessage == 'isChecked') {
                $this->addFlash('info', 'le status est déja actualisé');
            }
        } else {
            $this->addFlash('warning', '404 page introuvable');
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
        $submittedToken = $request->getPayload()->get('token');

        if ($this->isCsrfTokenValid('delete-subscription'. $subscription->getId(), $submittedToken) && $subscription) {
            $this->em->remove($subscription);
            $this->em->flush();

            $this->addFlash('success', 'La demande a été supprimée avec succès');

            return $this->redirectToRoute('app_admin_subscription_index');
        } else {
            $this->addFlash('danger', 'Accès non autorisée');

            return $this->redirectToRoute('app_logout');
        }
    }
}
