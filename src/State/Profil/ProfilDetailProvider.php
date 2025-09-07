<?php
// src/State/UserItemProvider.php

namespace App\State\Profil;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\Profil\ProfilDetailDto;
use App\Repository\UserRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProfilDetailProvider implements ProviderInterface
{
    public function __construct(
        private UserRepository $userRepository
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        // Récupération de l'utilisateur par ID
        $id = $uriVariables['id'];
        $user = $this->userRepository->find($id);
        
        if (!$user) {
            throw new NotFoundHttpException('Utilisateur non trouvé');
        }
        
        $authUser = $user->getAuthUser();
                
        // Création du DTO
        return new ProfilDetailDto(
            $user->getId(),
            $user->getFirstName(),
            $user->getLastName(),
            $user->getCreatedAt(),
            $user->getUpdatedAt(),
            $user->getPhone(),
            $user->getTeam()->getName(),
            $authUser ? $authUser->getEmail() : null,
            $authUser ? $authUser->getRoles() : null,
            $authUser ? $authUser->getLastLogin() : null
        );
    }
    
}