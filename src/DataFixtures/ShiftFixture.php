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
    private const SHIFT_DURATION = 8; // Durée max d'un shift en heures
    private const MIN_BREAK = 8; // Pause minimale entre shifts en heures

    public function load(ObjectManager $manager): void
    {
        $missions = $manager->getRepository(Mission::class)->findAll();
        $users = $manager->getRepository(User::class)->findAll();

        foreach ($missions as $mission) {
            $team = $mission->getTeam();
            $missionStart = $mission->getStart();
            $missionEnd = $mission->getEnd();

            // Filtrer les utilisateurs de la même équipe
            $teamUsers = array_values(array_filter($users, fn($user) => $user->getTeam() === $team));

            if (count($teamUsers) < 3) {
                continue; // Besoin d'au moins 3 users
            }

            shuffle($teamUsers); // Mélanger pour varier les affectations

            // Calculer la durée totale de la mission en heures
            $missionDuration = $missionStart->diff($missionEnd)->h;
            $missionDays = $missionStart->diff($missionEnd)->days;

            // 1. Shifts de CONNEXION (2 users)
            $connexionStart = clone $missionStart;
            $connexionEnd = (clone $connexionStart)->modify('+2 hours');
            $this->createShiftPair(
                $manager,
                $mission,
                array_slice($teamUsers, 0, 2),
                $connexionStart,
                $connexionEnd,
                'connexion'
            );

            // 2. Répartir les shifts de surveillance sur la durée de la mission
            $survUsers = array_slice($teamUsers, 2); // Utilisateurs disponibles pour surveillance
            $currentTime = (clone $connexionEnd)->modify('+' . self::MIN_BREAK . ' hours');
            $survIndex = 0;
            $lastSurvUser = null;

            while ($currentTime < $missionEnd) {
                $shiftEnd = (clone $currentTime)->modify('+' . self::SHIFT_DURATION . ' hours');
                
                // Ajuster si on dépasse la fin de mission
                if ($shiftEnd > $missionEnd) {
                    $shiftEnd = clone $missionEnd;
                }

                // Sélectionner un user différent du précédent
                $availableUsers = array_filter($survUsers, fn($u) => $u !== $lastSurvUser);
                if (empty($availableUsers)) {
                    $availableUsers = $survUsers; // Au cas où
                }
                $user = $availableUsers[array_rand($availableUsers)];

                $this->createShift(
                    $manager,
                    $mission,
                    $user,
                    clone $currentTime,
                    clone $shiftEnd,
                    'surveillance'
                );

                $lastSurvUser = $user;
                $currentTime = (clone $shiftEnd)->modify('+' . self::MIN_BREAK . ' hours');
                $survIndex++;
            }

            // 3. Shifts de DECONNEXION (2 users différents)
            $decoStart = (clone $missionEnd)->modify('-2 hours');
            $decoUsers = $this->getAvailableUsers($teamUsers, [$lastSurvUser], 2);
            $this->createShiftPair(
                $manager,
                $mission,
                $decoUsers,
                $decoStart,
                clone $missionEnd,
                'deconnexion'
            );
        }

        $manager->flush();
    }

    private function createShiftPair(ObjectManager $manager, Mission $mission, array $users, \DateTimeImmutable $start, \DateTimeImmutable $end, string $activity): void
    {
        foreach ($users as $user) {
            $this->createShift($manager, $mission, $user, $start, $end, $activity);
        }
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

    private function getAvailableUsers(array $allUsers, array $excludedUsers, int $count): array
    {
        $availableUsers = array_filter($allUsers, fn($user) => !in_array($user, $excludedUsers, true));
        
        if (count($availableUsers) < $count) {
            // Si pas assez d'users disponibles, on prend ceux qu'on peut
            $availableUsers = array_merge($availableUsers, $excludedUsers);
        }
        
        shuffle($availableUsers);
        return array_slice($availableUsers, 0, $count);
    }

    public function getDependencies(): array
    {
        return [
            MissionFixture::class,
            UserAuthFixture::class
        ];
    }
}