<?php

namespace App\Repository;

use App\Entity\PortfolioEntry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PortfolioEntry|null find($id, $lockMode = null, $lockVersion = null)
 * @method PortfolioEntry|null findOneBy(array $criteria, array $orderBy = null)
 * @method PortfolioEntry[]    findAll()
 * @method PortfolioEntry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PortfolioEntryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PortfolioEntry::class);
    }

    // /**
    //  * @return PortfolioEntry[] Returns an array of PortfolioEntry objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PortfolioEntry
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
