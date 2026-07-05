<?php

namespace App\Repository;

use App\Entity\Subscription;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Subscription>
 */
class SubscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subscription::class);
    }


       public function findAllOrdredBySubmittedAt(User $user): array
       {
           return $this->createQueryBuilder('s')
               ->orderBy('s.submittedAt', 'DESC')
               ->where('s.user = :user')
               ->setParameter('user', $user)
               ->getQuery()
               ->getResult()
           ;
       }

       public function findOneWidthQuoteVehicleFormulaDocument(int $id): ?Subscription
       {
           return $this->createQueryBuilder('s')
               ->leftJoin('s.quote', 'q')
               ->addSelect('q')
               ->leftJoin('q.vehicle', 'v')
               ->addSelect('v')
               ->leftJoin('q.formula', 'f')
               ->addSelect('f')
               ->leftJoin('s.documents', 'd')
               ->addSelect('d')
               ->Where('s.id = :id')
               ->setParameter('id', $id)
               ->getQuery()
               ->getOneOrNullResult()
           ;
       }

       public function findWidthQuoteVehicleFormulaDocument(): array
       {
           return $this->createQueryBuilder('s')
               ->leftJoin('s.quote', 'q')
               ->addSelect('q')
               ->leftJoin('s.user', 'u')
               ->addSelect('u')
               ->leftJoin('q.vehicle', 'v')
               ->addSelect('v')
               ->leftJoin('q.formula', 'f')
               ->addSelect('f')
               ->leftJoin('s.documents', 'd')
               ->addSelect('d')
               ->orderBy('s.submittedAt', 'DESC')
               ->getQuery()
               ->getResult()
           ;
       }
}
