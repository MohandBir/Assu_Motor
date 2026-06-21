<?php

namespace App\Repository;

use App\Entity\Quote;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Quote>
 */
class QuoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quote::class);
    }


       public function findWithVehicleAndFormula(User $user): array
       {
           return $this->createQueryBuilder('q')
               ->leftJoin('q.vehicle', 'v')
               ->addSelect('v')
               ->leftJoin('q.formula', 'f')
               ->addSelect('f')
               ->leftJoin('q.subscription', 's')
               ->addSelect('s')
               ->where("q.user = :user")
               ->setParameter('user', $user)
               ->addOrderBy('q.createdAt', 'DESC')
               ->getQuery()
               ->getResult()
           ;
       }

    //    public function findOneBySomeField($value): ?Quote
    //    {
    //        return $this->createQueryBuilder('q')
    //            ->andWhere('q.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
