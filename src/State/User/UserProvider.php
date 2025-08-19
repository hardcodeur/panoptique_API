<?php

namespace App\State\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;

use App\Dto\User\UserListDto;
use App\Repository\UserRepository;

class UserProvider implements ProviderInterface
{   
    public function __construct(
        private UserRepository $userRepository
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        // Récupération des utilisateurs
        $users = $this->userRepository->findAll();
        
        // Transformation en DTOs
        return array_map(function ($user) {
            $authUser = $user->getAuthUser();
                                        
            return new UserListDto(
                $user->getId(),
                $user->getFirstName()." ".$user->getLastName(),
                $user->getFirstName(),
                $user->getLastName(),
                $user->getPhone(),
                $user->getTeam()?->getId(),
                $user->getTeam()?->getName(),
                $user->getStatus(),
                $user->getCreatedAt(),
                $authUser ? $authUser->getEmail() : null,
                $authUser ? $authUser->getRoles() : null,
            );
        }, $users);
    }
}
