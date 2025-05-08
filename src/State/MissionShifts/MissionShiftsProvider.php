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
            // Initialiser la structure des shifts par activité
            $shiftsByActivity = [
                'connexion' => ['shift' => []],
                'surveillance' => ['shift' => []],
                'deconnexion' => ['shift' => []]
            ];
            
            // Grouper les shifts par activité
            foreach ($mission->getShifts() as $shift) {
                $user = $shift->getUser();
                $authUser = $user->getAuthUser();
                
                $shiftData = [
                    'start' => $shift->getStart(),
                    'end' => $shift->getEnd(),
                    'startHourFormat' => $shift->getStart()->format('H\hi'),
                    'endHourFormat' => $shift->getEnd()->format('H\hi'),
                    'users' => [
                        [
                            'id' => $user->getId(),
                            'userFullname' => $user->getFirstName().' '.$user->getLastName(),
                            'userRole' => $authUser->getRoles()
                        ]
                    ]
                ];
                
                $activity = $shift->getActivity();
                $shiftsByActivity[$activity]['shift'][] = $shiftData;
            }
            
            $customer = $mission->getCustomer();
            $team = $mission->getTeam();
            $location = $customer->getLocation();
            
            return [
                'id' => $mission->getId(),
                'start' => $mission->getStart(),
                'end' => $mission->getEnd(),
                'location' => $location->getName(),
                'teamName' => $team->getName(),
                'createdAt' => $mission->getCreatedAt()->format('d/m/Y H:i'),
                'shifts' => $shiftsByActivity
            ];
        }, $missions);
    }
}