<?php

namespace App\State\Mission;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\Mission\MissionShiftsDto;
use App\Repository\MissionRepository;

class MissionShiftsProvider implements ProviderInterface
{   

    public ?\IntlDateFormatter $dateFormatter = null;

    private function getDateFormatter(): \IntlDateFormatter
    {
        if ($this->dateFormatter === null) {
            $this->dateFormatter = new \IntlDateFormatter(
                'fr_FR', 
                \IntlDateFormatter::FULL, 
                \IntlDateFormatter::NONE,
                'Europe/Paris',
                \IntlDateFormatter::GREGORIAN,
                'EEEE d MMMM' // format "lundi 6 mai"
            );
        }
        
        return $this->dateFormatter;
    }

    public function __construct(
        private MissionRepository $missionRepository
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $missions = $this->missionRepository->findMissionsWithShifts();
        
        return array_map(function ($mission) {
            $shiftsByActivityAndTime = [
                'connexion' => [],
                'surveillance' => [],
                'deconnexion' => []
            ];
            
            // regrouper les shifts par activité et créneau horaire
            foreach ($mission->getShifts() as $shift) {
                $user = $shift->getUser();
                $authUser = $user->getAuthUser();
                $role= $authUser->getRoles();
                
                $timeKey = $shift->getStart()->format('U').'-'.$shift->getEnd()->format('U');
                $activity = $shift->getActivity();
                $date = $shift->getStart()->setTimezone(new \DateTimeZone('Europe/Paris'));
                
                if (!isset($shiftsByActivityAndTime[$activity][$timeKey])) {
                    $shiftsByActivityAndTime[$activity][$timeKey] = [
                        'start' => $shift->getStart(),
                        'end' => $shift->getEnd(),
                        'startDateFormat' => $this->getDateFormatter()->format($date),
                        'startHourFormat' => $shift->getStart()->format('H\hi'),
                        'endHourFormat' => $shift->getEnd()->format('H\hi'),
                        'users' => []
                    ];
                }
                
                $shiftsByActivityAndTime[$activity][$timeKey]['users'][] = [
                    'id' => $user->getId(),
                    'userFullname' => $user->getFirstName().' '.$user->getLastName(),
                    'userRole' => strtolower(str_replace('ROLE_', '', $role[0])),
                ];
            }
            
            // Deuxième passage : réorganiser dans la structure finale
            $shiftsByActivity = [
                'connexion' => ['shift' => []],
                'surveillance' => ['shift' => []],
                'deconnexion' => ['shift' => []]
            ];
            
            foreach ($shiftsByActivityAndTime as $activity => $timeSlots) {
                foreach ($timeSlots as $timeSlot) {
                    $shiftsByActivity[$activity]['shift'][] = $timeSlot;
                }
            }
            
            $customer = $mission->getCustomer();
            $team = $mission->getTeam();
            $location = $customer->getLocation();
            
            return new MissionShiftsDto(
                $mission->getId(),
                $mission->getStart(),
                $mission->getEnd(),
                $location->getName(),
                $team->getName(),
                $mission->getCreatedAt(),
                $mission->getUpdatedAt(),
                $shiftsByActivity
            );
        }, $missions);
    }

}