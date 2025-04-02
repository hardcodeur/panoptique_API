<?php
// src/State/UserItemProvider.php

namespace App\State\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\User\UserDetailDto;
use App\Repository\UserRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserItemProvider implements ProviderInterface
{
    public function __construct(
        private UserRepository $userRepository
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        // Récupération de l'utilisateur par ID
        $id = $uriVariables['id'] ?? null;
        $user = $this->userRepository->find($id);
        
        if (!$user) {
            throw new NotFoundHttpException('Utilisateur non trouvé');
        }
        
        $authUser = $user->getAuthUser();
        
        // Transformation des rôles pour meilleure lisibilité
        $roles = [];
        if ($authUser) {
            $roles = array_map(function($role) {
                return [
                    'code' => $role,
                    'name' => $this->formatRoleName($role)
                ];
            }, $authUser->getRoles());
        }
        
        // Création du DTO
        return new UserDetailDto(
            $user->getId(),
            $user->getFirstName(),
            $user->getLastName(),
            $user->getFirstName() . ' ' . $user->getLastName(),
            $user->getCreatedAt(),
            $user->getUpdatedAt(),
            $authUser ? $authUser->getEmail() : null,
            $roles,
            $authUser ? $authUser->getLastLogin() : null
        );
    }
    

    private function formatRoleName(string $role): string
    {
        $roleName = str_replace('ROLE_', '', $role);
        $roleName = strtolower($roleName);
        
        $roleMapping = [
            'admin' => 'Administrateur',
            'manager' => 'Gestionnaire',
            'team_manager' => 'Chef d\'équipe',
            'user' => 'Agent'
        ];
        
        return $roleMapping[$roleName] ?? ucfirst($roleName);
    }
}