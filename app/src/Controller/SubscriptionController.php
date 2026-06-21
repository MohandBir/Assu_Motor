<?php

namespace App\Controller;

use App\Entity\Quote;
use App\Entity\Subscription;
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
                //dd('docs uploaded and form is valid');
            }
            $subscription = $this->subscriptionManager->completeSubscription($quote);
            $quote->setStatus(Quote::SUBSCRIBED);

            $this->em->persist($subscription);
            $this->em->flush();
            $this->addFlash('info', 'Votre demande est crée , pour qu\'elle soit traitée ,veuillez charger vos documents');

            return $this->redirectToRoute('app_account_subscription');
        }

        return $this->render('subscription/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/quote/cancel/{id}', name: 'app_subscription_cancel', requirements: ['id' => '\d+'], defaults: ['id' => null])]
    public function delete(?Subscription $subscription, Request $request): Response
    {
        if ($this->getUser()) {
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

        return $this->redirectToRoute('app_login');
    }
}
