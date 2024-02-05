<?php

namespace App\Repository;

use App\Entity\IdUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<IdUser>
 *
 * @method IdUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method IdUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method IdUser[]    findAll()
 * @method IdUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IdUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, IdUser::class);
    }

//    /**
//     * @return IdUser[] Returns an array of IdUser objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?IdUser
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
