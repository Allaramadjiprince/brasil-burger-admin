<?php

namespace App\Repository;

use App\Entity\LigneCommande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LigneCommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LigneCommande::class);
    }

    public function findLignesParCommande(int $commandeId): array
    {
        return $this->createQueryBuilder('lc')
            ->join('lc.commande', 'c')
            ->where('c.id = :commandeId')
            ->setParameter('commandeId', $commandeId)
            ->orderBy('lc.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findTopProduits(): array
    {
        return $this->createQueryBuilder('lc')
            ->select('p.id, p.nom, SUM(lc.quantite) as quantite')
            ->join('lc.produit', 'p')
            ->join('lc.commande', 'c')
            ->where('c.statut != :annule')
            ->setParameter('annule', \App\Entity\Commande::STATUT_ANNULE)
            ->groupBy('p.id')
            ->orderBy('quantite', 'DESC')
            ->getQuery()
            ->getResult();
    }
}