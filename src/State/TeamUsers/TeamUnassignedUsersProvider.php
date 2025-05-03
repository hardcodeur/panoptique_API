<?php

namespace App\State\TeamUsers;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;

use App\Dto\User\UserListDto;
use App\Repository\UserRepository;

class TeamUnassignedUsersProvider implements ProviderInterface
{   
    public function __construct(
        private UserRepository $userRepository
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        // Récupération des utilisateurs
        $users = $this->userRepository->findUsersWithoutTeam();
        
        // Transformation en DTOs
        return array_map(function ($user) {   
            return new UserListDto(
                $user->getId(),
                $user->getFirstName()." ".$user->getLastName()
            );
        }, $users);
    }
}
