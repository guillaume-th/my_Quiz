<?php

namespace App\Repository;

use App\Entity\HistoriqueQuizz;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method HistoriqueQuizz|null find($id, $lockMode = null, $lockVersion = null)
 * @method HistoriqueQuizz|null findOneBy(array $criteria, array $orderBy = null)
 * @method HistoriqueQuizz[]    findAll()
 * @method HistoriqueQuizz[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistoriqueQuizzRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HistoriqueQuizz::class);
    }


    public function findUsersByQuizzTaken($quizzes, $taken)
    {
        // $symbol = $taken ? "=" : "!="; 
        $qb = $this->createQueryBuilder("h")
            ->select("DISTINCT h")
            ->where("h.categorie = :quizzes")
            ->setParameter("quizzes", $quizzes);
        $qry = $qb->getQuery();

        // if ($taken) {
        return $qry->execute();
        // } else {
        //     $quizzes = [];
        //     $data = $qry->execute();
        //     foreach($data as $d){
        //         array_push($quizzes, $d->getCategorie()->id);
        //     }
        //     $qb = $this->createQueryBuilder("h")
        //         ->select("DISTINCT h")
        //         ->where("h.categorie != :quizzes")
        //         ->setParameter("quizzes", $quizzes);
        //     $qry = $qb->getQuery();
        //     return $qry->execute();
        // }



        // $qry = $qb->getQuery();
        // dd($qry); 
        // $ids = $qry->execute()->id;
    }


    // /**
    //  * @return HistoriqueQuizz[] Returns an array of HistoriqueQuizz objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?HistoriqueQuizz
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
