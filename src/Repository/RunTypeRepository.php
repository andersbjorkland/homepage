<?php

namespace App\Repository;

use App\Entity\RunType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RunType|null find($id, $lockMode = null, $lockVersion = null)
 * @method RunType|null findOneBy(array $criteria, array $orderBy = null)
 * @method RunType[]    findAll()
 * @method RunType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RunTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RunType::class);
    }

    // /**
    //  * @return RunType[] Returns an array of RunType objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RunType
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
