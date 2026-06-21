<?php

namespace App\Service;

use App\Entity\Formula;
use App\Entity\Quote;
use App\Entity\User;
use App\Entity\Vehicle;
use App\Repository\ModelRepository;
use App\Repository\VehicleReferenceRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class QuoteManager  
{
    public function __construct(
        private ModelRepository $modelRepo,  
        private VehicleReferenceRepository $referenceRepo,  
        private EntityManagerInterface $em,
    ) {}

    public function getSessionQuote(SessionInterface $session): ?Quote
    {
        return $session->get('quote');
    }

    public function setSessionQuote(SessionInterface $session, Quote $quote): void
    {
        $session->set('quote', $quote);
    }

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

    public function rebuildFromSession(Quote $savedQuote ,Quote $quote, Vehicle $vehicle, User $user): Quote
    {

        $vehicle
            ->setType($quote->getVehicle()->getType())
            ->setBrand($quote->getVehicle()->getBrand())
            ->setModel($quote->getVehicle()->getModel())
            ->setVehicleYear($quote->getVehicle()->getVehicleYear())
            ->setLicensePlate($quote->getVehicle()->getLicensePlate())
        ;
        
        $savedQuote
            ->setDuration($quote->getDuration())
            ->setCreatedAt($quote->getCreatedAt())
            ->setExpiredAt($quote->getExpiredAt())
            ->setStatus($quote->getStatus())
            ->setLicenseYear($quote->getLicenseYear())
            ->setBirthDate($quote->getBirthDate())
            ->setBonusMalus($quote->getBonusMalus())
            ->setEstimatedPrice($quote->getEstimatedPrice())
            ->setEstimatedPrice($quote->getEstimatedPrice())
            //->setFormula($quote->getFormula())
            ->setVehicle($vehicle)
            ->setUser($user)
            ;
            
        return $savedQuote;
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
