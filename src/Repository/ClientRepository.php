<?php

namespace App\Repository;

use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    public function findAllOrderByNom(): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.nom', 'ASC')
            ->addOrderBy('c.prenom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findOneByEmail(string $email): ?Client
    {
        return $this->createQueryBuilder('c')
            ->where('c.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function searchClients(string $search): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.nom LIKE :search')
            ->orWhere('c.prenom LIKE :search')
            ->orWhere('c.email LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->orderBy('c.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }
}