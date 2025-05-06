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
        $activities = ['co', 'surv', 'deco'];
        
        foreach ($missions as $mission) {
            $missionStart = $mission->getStart();
            $missionEnd = $mission->getEnd();
            $team = $mission->getTeam();
            
            // Filtrer les utilisateurs de la même équipe que la mission
            $teamUsers = array_filter($users, function($user) use ($team) {
                return $user->getTeam() === $team;
            });
            
            if (empty($teamUsers)) {
                continue;
            }
            
            // Créer 3 shifts par mission (matin, après-midi, nuit)
            for ($i = 0; $i < 3; $i++) {
                $shift = new Shift();
                
                // Définir les heures de début/fin en fonction du créneau
                if ($i === 0) { // Matin (6h-14h)
                    $start = (clone $missionStart)->setTime(6, 0);
                    $end = (clone $missionStart)->setTime(14, 0);
                    $activitie = $activities[$i];
                } elseif ($i === 1) { // Après-midi (14h-22h)
                    $start = (clone $missionStart)->setTime(14, 0);
                    $end = (clone $missionStart)->setTime(22, 0);
                    $activitie = $activities[$i];
                } else { // Nuit (22h-6h)
                    $start = (clone $missionStart)->setTime(22, 0);
                    $end = (clone $missionStart)->add(new \DateInterval('P1D'))->setTime(6, 0);
                    $activitie = $activities[$i];
                }
                
                // Vérifier que le shift ne dépasse pas la fin de la mission
                if ($end > $missionEnd) {
                    $end = clone $missionEnd;
                }
                
                $shift->setStart($start);
                $shift->setEnd($end);
                $shift->setActivity($activitie);
                $shift->setMission($mission);
                
                $randomUser = $teamUsers[array_rand($teamUsers)];
                $shift->setUser($randomUser);
                
                $manager->persist($shift);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            MissionFixture::class,
            UserAuthFixture::class
        ];
    }
}