<?php

namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }

    public function findAllOrderByDate(): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findCommandesParStatut(string $statut): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.statut = :statut')
            ->setParameter('statut', $statut)
            ->orderBy('c.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findCommandesEnCours(): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.statut IN (:statuts)')
            ->setParameter('statuts', [
                Commande::STATUT_EN_ATTENTE,
                Commande::STATUT_VALIDE,
                Commande::STATUT_EN_PREPARATION,
                Commande::STATUT_PRET
            ])
            ->orderBy('c.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findCommandesAujourdhui(): array
    {
        $aujourdhui = new \DateTime();
        $aujourdhui->setTime(0, 0, 0);
        
        $demain = new \DateTime();
        $demain->setTime(23, 59, 59);

        return $this->createQueryBuilder('c')
            ->where('c.date >= :debut')
            ->andWhere('c.date <= :fin')
            ->setParameter('debut', $aujourdhui)
            ->setParameter('fin', $demain)
            ->orderBy('c.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findCommandesAnnuellesAujourdhui(): array
    {
        $aujourdhui = new \DateTime();
        $aujourdhui->setTime(0, 0, 0);
        
        $demain = new \DateTime();
        $demain->setTime(23, 59, 59);

        return $this->createQueryBuilder('c')
            ->where('c.date >= :debut')
            ->andWhere('c.date <= :fin')
            ->andWhere('c.statut = :annule')
            ->setParameter('debut', $aujourdhui)
            ->setParameter('fin', $demain)
            ->setParameter('annule', Commande::STATUT_ANNULE)
            ->orderBy('c.date', 'DESC')
            ->getQuery()
            ->getResult();
    }
}