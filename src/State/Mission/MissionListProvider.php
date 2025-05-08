<?php

namespace App\State\Mission;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;

use App\Dto\Mission\MissionListDto;
use App\Repository\MissionRepository;

class MissionListProvider implements ProviderInterface
{   
    public function __construct(
        private MissionRepository $missionRepository
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $missions = $this->missionRepository->findMissions();
        
        return array_map(function ($mission) {                                        
            return new MissionListDto(
                $mission->getId(),
                $mission->getStart(),
                $mission->getEnd(),
                $mission->getCustomer()->getName(),
                $mission->getCustomer()->getProduct(),
                $mission->getCustomer()->getLocation()->getName(),
                $mission->getCustomer()->getLocation()->getAddress(),
                $mission->getTeam()->getName(), 
            );
        }, $missions);
    }
}
