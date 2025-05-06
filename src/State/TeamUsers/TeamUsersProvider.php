<?php

namespace App\State\TeamUsers;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;

use App\Dto\TeamUsers\TeamUsersDto;
use App\Dto\User\UserDto;
use App\Repository\TeamRepository;

class TeamUsersProvider implements ProviderInterface
{   
    public function __construct(
        private TeamRepository $teamRepository
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        // Récupération de toutes les équipes avec leurs users
        $teams = $this->teamRepository->findTeamsUsers();
        
        // Transformation des équipes en DTO
        return array_map(function ($team) {
            // Construction du tableau de users pour chaque équipe
            $usersDto = [];
            foreach ($team->getUsers() as $user) {
                $authUser = $user->getAuthUser();
                $usersDto[] = new UserDto(
                    $user->getId(),
                    $user->getFirstName()." ".$user->getLastName(),
                    $authUser->getRoles(),
                    $user->getStatus()
                );
            }
            
            // Retourne un TeamUsersDto avec les utilisateurs
            return new TeamUsersDto(
                $team->getId(),
                $team->getName(),
                $usersDto
            );
        }, $teams);
    }
}
