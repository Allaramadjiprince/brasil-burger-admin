<?php

namespace App\Repository;

use App\Entity\Livreur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LivreurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livreur::class);
    }

    public function findAllOrderByNom(): array
    {
        return $this->createQueryBuilder('l')
            ->orderBy('l.nom', 'ASC')
            ->addOrderBy('l.prenom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findLivreursDisponibles(): array
    {
        return $this->createQueryBuilder('l')
            ->where('l.disponible = :disponible')
            ->setParameter('disponible', true)
            ->orderBy('l.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function searchLivreurs(string $search): array
    {
        return $this->createQueryBuilder('l')
            ->where('l.nom LIKE :search')
            ->orWhere('l.prenom LIKE :search')
            ->orWhere('l.telephone LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->orderBy('l.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }
}