<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

use \DateTime;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    /**
     * @return Post[] Returns an array of Post objects
     */
    public function getLatestPaginated($page, $limit)
    {
        $offset =($page - 1) * $limit;
        $now = new DateTime("now");
        return $this->createQueryBuilder('b')
            ->andWhere('b.publishTime < :now')
            ->setParameter('now', $now)
            ->orderBy('b.id', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Post[] Returns an array of Post objects
     */
    public function getUnpublished()
    {
        $now = new DateTime("now");
        return $this->createQueryBuilder('b')
            ->andWhere('b.publishTime > :now')
            ->setParameter('now', $now)
            ->orderBy('b.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Post Returns the latest published Post object
     */
    public function getLatestPost()
    {
        $now = new DateTime("now");
        $post = $this->createQueryBuilder('b')
            ->andWhere('b.publishTime < :now')
            ->setParameter('now', $now)
            ->orderBy('b.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult()
        ;

        return $post;
    }

    /**
     * @return int Number of items in repository
     */
    public function getCount()
    {
        $now = new DateTime("now");
        $result = $this->createQueryBuilder('b')
            ->andWhere('b.publishTime < :now')
            ->setParameter('now', $now)
            ->select('count(b.id)')
            ->getQuery()
            ->getSingleResult();
        ;
        $count = 0;
        foreach ($result as $c => $v) {
            $count = $v;
        }
        
        return $count;
    }

    // /**
    //  * @return Post[] Returns an array of Post objects
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
    public function findOneBySomeField($value): ?Post
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
