<?php

namespace App\Controller\Admin;

use App\Entity\Subscription;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminUserController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepo,
        private EntityManagerInterface $em,
    ) {}

    #[Route('/admin/user', name: 'app_admin_user_index')]
    public function index(): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', 'accès non autorisé, veuillez vous connecter au compte approprié');

            return $this->redirectToRoute('app_login');
        }

        $users = $this->userRepo->findWithSubscription();
        //dd($users);

        return $this->render('admin/user/index.html.twig', [
            'users' => $users,
        ]);
    }
    
    #[Route('/admin/user/show/{id}', name: 'app_admin_user_show', requirements: ['id' => '\d+'], defaults: ['id' => null])]
    public function show(?User $user): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', 'accès non autorisé, veuillez vous connecter au compte approprié');

            return $this->redirectToRoute('app_login');
        }
        if($user) {
            $user = $this->userRepo->findWithSubscription($user->getId());
        } else {
            $this->addFlash('warning', '404 page introuvable');

            return $this->redirectToRoute('app_admin_user_index');
        }

        $pendingCount = 0;
        $validatedCount = 0;
        foreach( $user->getSubscriptions() as $subscription) {
            if ($subscription->getStatus() === Subscription::PENDING_REVIEW) $pendingCount += 1;
            if ($subscription->getStatus() === Subscription::VALIDATED) $validatedCount += 1;
        }

        return $this->render('admin/user/show.html.twig', [
            'user' => $user,
            'pendingCount' => $pendingCount,
            'validatedCount' => $validatedCount,
        ]);
    }
    
    #[Route('/admin/user/delete/{id}}', name: 'app_admin_user_delete')]
    public function delete(?User $user, Request $request): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', 'accès non autorisé, veuillez vous connecter au compte approprié');

            return $this->redirectToRoute('app_login');
        }

        if (!$user) {
            $this->addFlash('danger', 'Accès non autorisée');

            return $this->redirectToRoute('app_admin_user_index');
        }

        $submittedToken = $request->getPayload()->get('token');
        if ($this->isCsrfTokenValid('delete-user'. $user->getId(), $submittedToken)) {

            $this->em->remove($user);
            $this->em->flush();

            $this->addFlash('success', 'L\'utilisateure a été supprimée avec succès');

            return $this->redirectToRoute('app_admin_user_index');
        } else {
            $this->addFlash('danger', 'Accès non autorisée');

            return $this->redirectToRoute('app_logout');
        }
    }
}
