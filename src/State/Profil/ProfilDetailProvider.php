<?php
// src/State/UserItemProvider.php

namespace App\State\Profil;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\Profil\ProfilDetailDto;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class ProfilDetailProvider implements ProviderInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private Security $security
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        /** @var \App\Entity\AuthUser|null $authUser */
        $authUser = $this->security->getUser();

        if (!$authUser) {
            throw new UnauthorizedHttpException('Bearer', 'User not authenticated.');
        }
        
        $user = $authUser->getUser();
                
        // Création du DTO
        return new ProfilDetailDto(
            $user->getId(),
            $user->getFirstName(),
            $user->getLastName(),
            $user->getCreatedAt(),
            $user->getUpdatedAt(),
            $user->getPhone(),
            $user->getTeam()?->getName(),
            $authUser->getEmail(),
            $authUser->getRoles(),
            $authUser->getLastLogin()
        );
    }
    
}