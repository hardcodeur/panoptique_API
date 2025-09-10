<?php

namespace App\Repository;

use App\Entity\Mission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;

/**
 * @extends ServiceEntityRepository<Mission>
 */
class MissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mission::class);
    }

    public function findCurrentAndFutureMissions(): array
    {
        $todayStart = new \DateTimeImmutable('today');

        return $this->createQueryBuilder('m')
            ->where('m.end >= :today')
            ->setParameter('today', $todayStart)
            ->orderBy('m.start', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findPastMissions(): array
    {
        $todayStart = new \DateTimeImmutable('today');
    
        return $this->createQueryBuilder('m')
            ->where('m.start < :todayStart')
            ->setParameter('todayStart', $todayStart)
            ->orderBy('m.start', 'DESC')
            ->getQuery()
            ->getResult();
    }


    public function findMissionsWithShifts()
    {
        $todayStart = (new \DateTimeImmutable('today'))->setTime(0,0);

        return $this->createQueryBuilder('m')
            ->leftJoin('m.shifts', 's')
            ->addSelect('s')
            ->leftJoin('s.user', 'u')
            ->addSelect('u')
            ->leftJoin('m.customer', 'c')
            ->addSelect('c')
            ->leftJoin('c.location', 'l')
            ->addSelect('l')
            ->leftJoin('m.team', 't')
            ->addSelect('t')
            ->where('m.start >= :todayStart')
            ->setParameter('todayStart', $todayStart)
            ->orderBy('m.start', 'ASC')
            ->addOrderBy('s.start', 'ASC')
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Mission[] Returns an array of Mission objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Mission
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
