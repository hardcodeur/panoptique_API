<?php

namespace App\State\User;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;

use App\Dto\User\UserListDto;
use App\Repository\UserRepository;

class UserTeamProvider implements ProviderInterface
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
        $users = $this->userRepository->findActiveUsersTeam($user->getTeam()->getId());
        
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
