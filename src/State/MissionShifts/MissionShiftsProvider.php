<?php

namespace App\State\MissionShifts;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;

use App\Dto\MissionShifts\MissionShiftsDto;
use App\Dto\Shift\ShiftDto;
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
            $shiftsByActivity = [
                'connexion' => [],
                'surveillance' => [],
                'deconnexion' => []
            ];
            
            foreach ($mission->getShifts() as $shift) {
                $user = $shift->getUser();
                $authUser = $user->getAuthUser();
                
                $shiftDto = new ShiftDto(
                    $shift->getId(),
                    $shift->getStart(),
                    $shift->getEnd(),
                    $shift->getActivity(),
                    $user->getFirstName()." ".$user->getLastName(),
                    $authUser->getRoles()
                );
                
                $shiftsByActivity[$shift->getActivity()][] = $shiftDto;
            }
            
            $customer = $mission->getCustomer();
            $team = $mission->getTeam();
            $location = $customer ? $customer->getLocation() : null;
            
            return new MissionShiftsDto(
                $mission->getId(),
                $mission->getStart(),
                $mission->getEnd(),
                $location ? $location->getName() : null,
                $team ? $team->getName() : null,
                $mission->getCreatedAt(),
                $mission->getUpdatedAt(),
                $shiftsByActivity
            );
        }, $missions);
    }
}
