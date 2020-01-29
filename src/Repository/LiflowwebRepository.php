<?php

namespace App\Repository;

use App\Entity\Liflowweb;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Liflowweb|null find($id, $lockMode = null, $lockVersion = null)
 * @method Liflowweb|null findOneBy(array $criteria, array $orderBy = null)
 * @method Liflowweb[]    findAll()
 * @method Liflowweb[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LiflowwebRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Liflowweb::class);
    }

    // /**
    //  * @return Liflowweb[] Returns an array of Liflowweb objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Liflowweb
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
