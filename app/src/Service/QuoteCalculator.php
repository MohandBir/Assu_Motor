<?php

namespace App\Service;

use App\Entity\Quote;
use DateTime;



class QuoteCalculator  
{

    public function getPrice(Quote $quote, int $basePrice)
    {
        $seniorityCoef = $this->getSeniorityCoef($quote->getLicenseYear());
        $ageCoef = $this->getAgeCoef($quote->getBirthDate());
        $durationCoef = $this->getDurationCoef($quote->getDuration());
        $bonusMalusCoef = $this->getBonusMalusCoef($quote->getBonusMalus());

        $estimatedPrice = $basePrice * $seniorityCoef * $ageCoef * $durationCoef * $bonusMalusCoef;

        return $estimatedPrice;

    }

    private function getSeniorityCoef(int $licenseYear)
    {
        $currentYear = (int) date('Y');
        $years = $currentYear - $licenseYear;
        if ($years < 1 ) return 1.8;
        if ($years >= 1 && $years < 2) return 1.50;
        if ($years >= 2 && $years < 3) return 1.25;
        if ($years >= 3 && $years < 10) return 1;
        if ($years >= 10 && $years < 20) return 0.95;
        if ($years >= 20) return 0.90;
    }

    private function getAgeCoef(DateTime $birthDate) 
    {
        $today = new DateTime();
        $age = $birthDate->diff($today)->y;

        if ($age <= 22) return 1.60;
        if ($age >= 23 && $age <= 30) return 1.30;
        if ($age >= 31 && $age <= 50) return 1.10;
        if ($age >= 51 && $age <= 65) return 0.95;
        if ($age >= 66 && $age <= 75) return 1.10;
        if ($age > 75) return 1.30;
      
    }

    private function getDurationCoef(int $duration) 
    {
        return (int) $duration;
    }

    private function getBonusMalusCoef(float $bonusMalus): float 
    {
        return $bonusMalus;
    }
}
