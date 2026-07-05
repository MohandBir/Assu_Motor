<?php

namespace App\Service;

use App\Entity\Formula;
use App\Entity\Quote;
use App\Repository\VehicleReferenceRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class QuoteManager  
{
    public function __construct(
        private VehicleReferenceRepository $referenceRepo,  
        private EntityManagerInterface $em,
    ) {}

    public function isValidVehicle(Quote $quote)
    {
        $brand = $quote->getVehicle()->getBrand();
        $model = $quote->getVehicle()->getModel();
        $vehicleYear = $quote->getVehicle()->getVehicleYear();

        $vehicleReference = $this->referenceRepo->findWithModelAndBrand($vehicleYear, $brand, $model);
        
        
        if (!$vehicleReference) {
           return false;
        }

        return true;
    }

    public function completeFormDataQuote(Quote $quote): Quote
    {
        $quote
            ->setBonusMalus(1)
            ->setStatus(Quote::PENDING)
            ->setCreatedAt(new DateTimeImmutable('now'))
            ->setExpiredAt(new DateTimeImmutable('+1 month'))
        ;

        return $quote;
    }

    public function reatacheFormula($quote=null): ?Quote
    {
        if ($quote !== null) {
            if ($quote->getFormula() !== null) {
                $quote->setFormula(
                    $this->em->find(Formula::class, $quote->getFormula()->getId())
                );

                return $quote;
            }
        }

        return null;
    }

}
