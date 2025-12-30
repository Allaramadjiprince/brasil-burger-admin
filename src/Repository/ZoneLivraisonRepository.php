<?php

namespace App\Repository;

use App\Entity\ZoneLivraison;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ZoneLivraisonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ZoneLivraison::class);
    }

    /**
     * Trouve les zones par prix croissant
     */
    public function findAllOrderByPrix(): array
    {
        return $this->createQueryBuilder('z')
            ->orderBy('z.prix', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve une zone contenant un quartier spécifique
     */
    public function findZoneByQuartier(string $quartier): ?ZoneLivraison
    {
        $zones = $this->createQueryBuilder('z')
            ->getQuery()
            ->getResult();

        foreach ($zones as $zone) {
            if ($zone->quartierEstDansZone($quartier)) {
                return $zone;
            }
        }

        return null;
    }

    /**
     * Trouve les zones avec le plus de commandes
     */
    public function findMostPopularZones(int $limit = 5): array
    {
        return $this->createQueryBuilder('z')
            ->leftJoin('z.commandes', 'c')
            ->groupBy('z.id')
            ->orderBy('COUNT(c.id)', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}