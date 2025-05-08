<?php

namespace App\State\MissionShifts;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\MissionShifts\MissionShiftsDto;
use App\Repository\MissionRepository;

class MissionShiftsProvider implements ProviderInterface
{   
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
                
                if (!isset($shiftsByActivityAndTime[$activity][$timeKey])) {
                    $shiftsByActivityAndTime[$activity][$timeKey] = [
                        'start' => $shift->getStart(),
                        'end' => $shift->getEnd(),
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