<?php

namespace App\State\Team;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;

use App\Dto\Team\TeamListDto;
use App\Repository\TeamRepository;

class TeamProvider implements ProviderInterface
{   
    public function __construct(
        private TeamRepository $teamRepository
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $teams = $this->teamRepository->findAll();
        
        return array_map(function ($team) {                                        
            return new TeamListDto(
                $team->getId(),
                $team->getName(),
            );
        }, $teams);
    }
}
