<?php

namespace App\Repository;

use App\Entity\Mission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Mission>
 */
class MissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mission::class);
    }

    public function findMissionShifts(): array
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.shifts', 'u')
            ->addSelect('u')
            ->getQuery()
            ->getResult();
    }

    public function findMissions(): array
    {
        $todayStart = new \DateTimeImmutable('today');

        return $this->createQueryBuilder('m')
            ->where('m.start >= :todayStart')
            ->setParameter('todayStart', $todayStart)
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
