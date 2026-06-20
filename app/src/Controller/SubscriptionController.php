<?php

namespace App\Controller;

use App\Entity\Quote;
use App\Form\SubscriptionType;
use App\Service\SubscriptionManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SubscriptionController extends AbstractController
{
    public function __construct(
        private SubscriptionManager $subscriptionManager,
        private EntityManagerInterface $em,
    ) {}    

    #[Route('/subscription/new/{id}', name: 'app_subscription_new')]
    public function new(Quote $quote, Request $request): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(SubscriptionType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if($form->isValid()) {
                dd('docs uploaded and form is valid');
            }
            $subscription = $this->subscriptionManager->completeSubscription($quote);

            $this->em->persist($subscription);
            $this->em->flush();
            $this->addFlash('info', 'Votre demande est crée , pour qu\'elle soit traitée ,veuillez charger vos documents');

            return $this->redirectToRoute('app_account_subscription');
        }

        return $this->render('subscription/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
