<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminSubscriptionController extends AbstractController
{
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
            $this->addFlash('danger', 'accès non autorisé, veuillez vous connecter au compte coordonnée appropriés');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('admin/subscription/index.html.twig', [
        
        ]);
    }
}
