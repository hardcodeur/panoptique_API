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
        
        $missions = $this->missionRepository->findMissionShifts();
        
        return array_map(function ($mission) {
            $shiftDto = [];
            foreach ($mission->getShifts() as $shift) {
                $user = $shift->getUser();
                $authUser = $user->getAuthUser();
                $shiftDto[] = new ShiftDto(
                    $shift->getId(),
                    $shift->getStart(),
                    $shift->getEnd(),
                    $shift->getActivity(),
                    $user->getFirstName()." ".$user->getLastName(),
                    $authUser->getRoles(),
                );
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
                $shiftDto
            );
        }, $missions);
    }
}
