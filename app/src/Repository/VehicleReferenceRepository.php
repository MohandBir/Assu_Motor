<?php

namespace App\Repository;

use App\Entity\VehicleReference;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<VehicleReference>
 */
class VehicleReferenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VehicleReference::class);
    }

       public function findWithModelAndBrand(string $vehicleYear, string $brand, string $model): ?VehicleReference
       {
           return $this->createQueryBuilder('v')
               ->leftJoin('v.model', 'm')
               ->addSelect('m')
               ->leftJoin('m.brand', 'b')
               ->addSelect('b')
               ->where('v.year = :vehicleYear')
               ->setParameter('vehicleYear', $vehicleYear)
               ->andWhere('m.name = :model')
               ->setParameter('model', $model)
               ->andWhere('b.name = :brand')
               ->setParameter('brand', $brand)
               ->getQuery()
               ->getOneOrNullResult()
           ;
       }


}
