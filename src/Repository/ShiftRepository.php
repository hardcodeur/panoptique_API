<?php

namespace App\Repository;

use App\Entity\Shift;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Shift>
 */
class ShiftRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Shift::class);
    }

    public function findUserShiftsForCurrentWeek(int $userId): array
    {
        // Date du jour à minuit (00:00:00)
        $startDate = (new \DateTime())->setTime(0, 0, 0);
        
        // Fin de la semaine (dimanche à 23:59:59)
        $endOfWeek = (new \DateTime('sunday this week'))->setTime(23, 59, 59);

        return $this->createQueryBuilder('s')
            ->join('s.user', 'u')
            ->join('s.mission', 'm')
            ->where('u.id = :userId')
            ->andWhere('s.start BETWEEN :start AND :end')
            ->setParameter('userId', $userId)
            ->setParameter('start', $startDate)
            ->setParameter('end', $endOfWeek)
            ->orderBy('s.start', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findUseShiftMetric(int $userId): array
    {
        $startOfMonth = new \DateTime('first day of this month');
        $endOfMonth = new \DateTime('last day of this month 23:59:59');
    
        // Récupérer tous les shifts concernés
        $shifts = $this->createQueryBuilder('s')
            ->where('s.user = :userId')
            ->andWhere('s.start BETWEEN :start AND :end')
            ->setParameter('userId', $userId)
            ->setParameter('start', $startOfMonth)
            ->setParameter('end', $endOfMonth)
            ->getQuery()
            ->getResult();
    
        // Initialiser le compteur avec toutes les activités possibles à 0
        $activitiesCount = [
            'connexion' => 0,
            'surveillance' => 0,
            'deconnexion' => 0
        ];
        
        $totalHours = 0;
        
        foreach ($shifts as $shift) {
            // Calcul du temps en heures
            $diff = $shift->getEnd()->getTimestamp() - $shift->getStart()->getTimestamp();
            $totalHours += $diff / 3600;
            
            // Incrémenter le compteur pour cette activité
            $activity = $shift->getActivity();
            if (array_key_exists($activity, $activitiesCount)) {
                $activitiesCount[$activity]++;
            }
        }
    
        return [
            'userId' => $userId,
            'month' => (int)$startOfMonth->format('m'),
            'monthName' => $startOfMonth->format('F Y'),
            'totalHours' => round($totalHours, 2),
            'totalShifts' => count($shifts),
            'activitiesCount' => $activitiesCount
        ];
    }

//    /**
//     * @return Shift[] Returns an array of Shift objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Shift
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
