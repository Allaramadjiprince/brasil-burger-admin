<?php

namespace App\Repository;

use App\Entity\Paiement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PaiementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Paiement::class);
    }

    public function findAllOrderByDate(): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findPaiementsParStatut(string $statut): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.statut = :statut')
            ->setParameter('statut', $statut)
            ->orderBy('p.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findPaiementsAujourdhui(): array
    {
        $aujourdhui = new \DateTime();
        $aujourdhui->setTime(0, 0, 0);
        
        $demain = new \DateTime();
        $demain->setTime(23, 59, 59);

        return $this->createQueryBuilder('p')
            ->where('p.date >= :debut')
            ->andWhere('p.date <= :fin')
            ->setParameter('debut', $aujourdhui)
            ->setParameter('fin', $demain)
            ->orderBy('p.date', 'DESC')
            ->getQuery()
            ->getResult();
    }
}