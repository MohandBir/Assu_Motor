<?php

namespace App\Service;

use App\Entity\Quote;
use App\Entity\Subscription;
use DateTimeImmutable;

class SubscriptionManager  
{
    public function __construct(

    ) {}

    public function completeSubscription(Quote $quote): ?Subscription
    {
        $reference = $quote->getUser()->getFname(). $quote->getUser()->getlname() . uniqid();
        $subscription = (new Subscription())
            ->setReferenceNumber($reference)
            ->setStatus(Subscription::PENDING_DOCUMENTS)
            ->setSubmittedAt(new DateTimeImmutable('now'))
            ->setUser($quote->getUser())
            ->setQuote($quote)
        ;

        return $subscription;
    }

    

}
