<?php

namespace App\State\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\User\UserCreateDto;
use App\Dto\User\UserUpdateDto;
use App\Dto\User\UserDetailDto;
use App\Entity\User;
use App\Entity\AuthUser;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private UserRepository $userRepository
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($data instanceof UserCreateDto) {
            return $this->handleCreate($data);
        } elseif ($data instanceof UserUpdateDto) {
            $userId = $uriVariables['id'] ?? null;
            return $this->handleUpdate($userId,$data);
        }

        return $data;
    }

    private function handleCreate(UserCreateDto $data): UserDetailDto
    {
        // Création d'un nouvel utilisateur
        $user = new User();
        $user->setFirstName($data->getFirstName());
        $user->setLastName($data->getLastName());

        // Création d'un nouvel utilisateur d'authentification
        $authUser = new AuthUser();
        $authUser->setEmail($data->getEmail());

        // Hashage du mot de passe
        $hashedPassword = $this->hashPassword($data->getPassword());
        $authUser->setPassword($hashedPassword);

        // Attribution des rôles
        $authUser->setRoles($data->getRole());

        // Liaison entre User et AuthUser
        $user->setAuthUser($authUser);

        // Persistance en base de données
        $this->entityManager->persist($user);
        $this->entityManager->persist($authUser);
        $this->entityManager->flush();

        return new UserDetailDto(
            $user->getId(),
            $user->getFirstName(),
            $user->getLastName(),
            $user->getCreatedAt(),
            $user->getUpdatedAt(),
            $authUser ? $authUser->getEmail() : null,
            $authUser ? $authUser->getRoles() : null,
        );
    }

    private function handleUpdate(int $userId,UserUpdateDto $data): UserDetailDto
    {
        // Récupération de l'utilisateur existant
        $user = $this->userRepository->find($userId);
        
        if (!$user) {
            throw new NotFoundHttpException(sprintf('Utilisateur avec ID %d introuvable', $userId));
        }

        $authUser = $user->getAuthUser();

        // Mise à jour des champs si fournis
        if ($data->getFirstName() !== null) {
            $user->setFirstName($data->getFirstName());
        }

        if ($data->getLastName() !== null) {
            $user->setLastName($data->getLastName());
        }

        if ($data->getEmail() !== null) {
            $authUser->setEmail($data->getEmail());
        }

        if ($data->getPassword() !== null) {
            $hashedPassword = $this->hashPassword($data->getPassword());
            $authUser->setPassword($hashedPassword);
        }

        if ($data->getRole() !== null) {
            $authUser->setRoles($data->getRole());
        }

        // Persistance des modifications
        $this->entityManager->flush();

        return new UserDetailDto(
            $user->getId(),
            $user->getFirstName(),
            $user->getLastName(),
            $user->getCreatedAt(),
            $user->getUpdatedAt(),
            $authUser ? $authUser->getEmail() : null,
            $authUser ? $authUser->getRoles() : null,
        );
    }

    private function hashPassword(string $plainPassword): string
    {
        return $this->passwordHasher->hashPassword(
            new class($plainPassword) implements PasswordAuthenticatedUserInterface {
                public function __construct(private string $password)
                {
                }
                
                public function getPassword(): ?string
                {
                    return $this->password;
                }
                
                public function getUserIdentifier(): string
                {
                    return '';
                }
            },
            $plainPassword
        );
    }
}