<?php

namespace App\Repository;

use App\Entity\Location;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Location>
 */
class LocationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Location::class);
    }

    public function findLocationsWithNotesByUser(int $userId): array
    {
        return $this->createQueryBuilder('location')
            ->leftJoin('location.locationNotes', 'note')
            ->addSelect('note')
            ->innerJoin('location.team', 'team')
            ->innerJoin('team.users', 'user')
            ->andWhere('user.id = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    public function findLocationsWithNotes(): array
    {
        return $this->createQueryBuilder('location')
            ->leftJoin('location.locationNotes', 'note') // Notez le camelCase
            ->addSelect('note')
            ->getQuery()
            ->getResult();
    }
    
//    /**
//     * @return Location[] Returns an array of Location objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Location
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
