<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    public function findAllOrderByNom(): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByType(string $type): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.type = :type')
            ->setParameter('type', $type)
            ->orderBy('p.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findBurgers(): array
    {
        return $this->findByType(Produit::TYPE_BURGER);
    }

    public function findMenus(): array
    {
        return $this->findByType(Produit::TYPE_MENU);
    }

    public function findComplements(): array
    {
        return $this->findByType(Produit::TYPE_COMPLEMENT);
    }

    public function searchProduits(string $search): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.nom LIKE :search')
            ->orWhere('p.description LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->orderBy('p.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }
}