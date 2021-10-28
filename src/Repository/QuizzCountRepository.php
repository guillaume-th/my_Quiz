<?php

namespace App\Repository;

use App\Entity\QuizzCount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method QuizzCount|null find($id, $lockMode = null, $lockVersion = null)
 * @method QuizzCount|null findOneBy(array $criteria, array $orderBy = null)
 * @method QuizzCount[]    findAll()
 * @method QuizzCount[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuizzCountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuizzCount::class);
    }

    public function findQuizzesLastPeriod($datetime)
    {
        $qb = $this->createQueryBuilder("q")
        ->select("count(q.id)")
        ->where("q.time >= :datetime")
        ->setParameter("datetime", $datetime);

       return $qb->getQuery()->getSingleScalarResult(); 

        // return $qry->execute(); 
    }


    // /**
    //  * @return QuizzCount[] Returns an array of QuizzCount objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('q.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?QuizzCount
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
