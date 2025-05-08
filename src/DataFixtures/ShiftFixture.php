<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Shift;
use App\Entity\Mission;
use App\Entity\User;

class ShiftFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {   
        $missions = $manager->getRepository(Mission::class)->findAll();
        $users = $manager->getRepository(User::class)->findAll();
        
        foreach ($missions as $mission) {
            $team = $mission->getTeam();
            $missionStart = $mission->getStart();
            $missionEnd = $mission->getEnd();
            
            // Filtrer les utilisateurs de la même équipe
            $teamUsers = array_filter($users, fn($user) => $user->getTeam() === $team);
            
            if (count($teamUsers) < 2) {
                continue; // On a besoin d'au moins 2 users pour co/deco
            }
            
            // 1. Shift de CONNEXION (2 users)
            $this->createShiftPair(
                $manager,
                $mission,
                $teamUsers,
                $missionStart->setTime(6, 0), // Début à 6h
                $missionStart->setTime(8, 0), // Fin à 8h
                'co'
            );
            
            // 2. Shift de SURVEILLANCE (1 user)
            $this->createShift(
                $manager,
                $mission,
                $teamUsers[array_rand($teamUsers)],
                $missionStart->setTime(8, 0),  // Début à 8h
                $missionStart->setTime(16, 0), // Fin à 16h
                'surv'
            );
            
            // 3. Second shift de SURVEILLANCE (1 user différent)
            $surveillanceUser = $this->getDifferentUser($teamUsers, $teamUsers[array_rand($teamUsers)]);
            $this->createShift(
                $manager,
                $mission,
                $surveillanceUser,
                $missionStart->setTime(16, 0), // Début à 16h
                $missionStart->setTime(22, 0),  // Fin à 22h
                'surv'
            );
            
            // 4. Shift de DECONNEXION (2 users)
            $this->createShiftPair(
                $manager,
                $mission,
                $teamUsers,
                $missionStart->setTime(22, 0),  // Début à 22h
                $missionStart->add(new \DateInterval('P1D'))->setTime(6, 0), // Fin à 6h du lendemain
                'deco'
            );
        }

        $manager->flush();
    }

    private function createShiftPair(ObjectManager $manager, Mission $mission, array $users, \DateTimeImmutable $start, \DateTimeImmutable $end, string $activity): void
    {
        // Prendre 2 users différents
        $user1 = $users[array_rand($users)];
        $user2 = $this->getDifferentUser($users, $user1);
        
        // Créer deux shifts identiques avec des users différents
        $this->createShift($manager, $mission, $user1, $start, $end, $activity);
        $this->createShift($manager, $mission, $user2, $start, $end, $activity);
    }

    private function createShift(ObjectManager $manager, Mission $mission, User $user, \DateTimeImmutable $start, \DateTimeImmutable $end, string $activity): void
    {
        $shift = new Shift();
        $shift->setStart($start);
        $shift->setEnd($end);
        $shift->setActivity($activity);
        $shift->setMission($mission);
        $shift->setUser($user);
        
        $manager->persist($shift);
    }

    private function getDifferentUser(array $users, User $excludeUser): User
    {
        $availableUsers = array_filter($users, fn($user) => $user !== $excludeUser);
        return $availableUsers[array_rand($availableUsers)];
    }

    public function getDependencies(): array
    {
        return [
            MissionFixture::class,
            UserAuthFixture::class
        ];
    }
}