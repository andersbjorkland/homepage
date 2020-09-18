<?php

namespace App\Repository;

use App\Entity\BlogPost;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

use \DateTime;

/**
 * @method BlogPost|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlogPost|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlogPost[]    findAll()
 * @method BlogPost[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlogPostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlogPost::class);
    }

    /**
     * @return BlogPost[] Returns an array of BlogPost objects
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
     * @return BlogPost[] Returns an array of BlogPost objects
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
     * @return BlogPost Returns the latest published BlogPost object
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
            ->getResult()
        ;
        dump($post);
        if (count($post) > 0) {
            $post = $post[0];
        } else {
            $post = null;
        }

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
