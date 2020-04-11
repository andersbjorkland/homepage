<?php

namespace App\Repository;

use App\Entity\Drum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Drum|null find($id, $lockMode = null, $lockVersion = null)
 * @method Drum|null findOneBy(array $criteria, array $orderBy = null)
 * @method Drum[]    findAll()
 * @method Drum[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DrumRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Drum::class);
    }

    // /**
    //  * @return Drum[] Returns an array of Drum objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Drum
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
