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
                            
            // Déterminer le rôle principal (le premier de la liste)
            $roleName = 'Utilisateur';
            if ($authUser && !empty($authUser->getRoles())) {
                $roles = $authUser->getRoles();
                // Simplification du nom du rôle pour l'affichage
                $roleName = str_replace('ROLE_', '', $roles[0]);
                $roleName = ucfirst(strtolower($roleName));
            }
                            
            return new UserListDto(
                $user->getId(),
                $user->getFirstName(),
                $user->getLastName(),
                $user->getCreatedAt(),
                $authUser ? $authUser->getEmail() : null,
                $roleName
            );
        }, $users);
    }
}
