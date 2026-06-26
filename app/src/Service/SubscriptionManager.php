<?php

namespace App\Service;

use App\Entity\Quote;
use App\Entity\Subscription;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class SubscriptionManager  
{
    public function __construct(
        private EntityManagerInterface $em,
        private DocumentManager $documentManager,
    ) {}

    public function completeSubscription(Quote $quote): ?Subscription
    {
        $reference = $quote->getUser()->getFname()[0]. $quote->getUser()->getlname()[0] . uniqid();
        $subscription = (new Subscription())
            ->setReferenceNumber($reference)
            ->setStatus(Subscription::PENDING_DOCUMENTS)
            ->setSubmittedAt(new DateTimeImmutable('now'))
            ->setUser($quote->getUser())
            ->setQuote($quote)
        ;

        return $subscription;
    }

    public function updateSubscription(Subscription $subscription): ?Subscription
    {
        $subscription->setStatus(Subscription::PENDING_REVIEW);
        
        return $subscription;
    }

    public function changeStatus(Subscription $subscription, string $statusTarget): ?string
    {     
        $acceptedStatus = [
            Subscription::VALIDATED,
            Subscription::DOCUMENTS_INVALID,
            Subscription::REJECTED,
        ];
        if (!in_array($statusTarget, $acceptedStatus)) return null;

        if ($statusTarget == $subscription->getStatus()) {
            $flashMessage = 'isAlreadyChanged';
        
            return $flashMessage;
        }
        // on supprime les documents de la base de données et dans le serveur

        $subscription->setStatus($statusTarget);

        if(!$subscription->getProcessedAt()) {
            $subscription->setProcessedAt(new DateTimeImmutable('now'));
        }
        $flashMessage = 'Le status est mis à jour avec succès';
        
        
        return $flashMessage;
    }

    

} 
