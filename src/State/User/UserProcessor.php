<?php

namespace App\State\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\User\UserCreateDto;
use App\Dto\User\UserUpdateDto;
use App\Dto\User\UserDetailDto;
use App\Entity\User;
use App\Entity\AuthUser;
use App\Repository\TeamRepository;
use App\Repository\AuthUserRepository;
use App\Repository\UserRepository;
use App\Service\PasswordGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PasswordGenerator $passwordGenerator,
        private UserPasswordHasherInterface $passwordHasher,
        private UserRepository $userRepository,
        private TeamRepository $teamRepository,
        private AuthUserRepository $authUserRepository,
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
        // check if isset same email in my bdd (email is unique)
        $userInBdd= $this->authUserRepository->findOneBy(['email' => $data->getEmail()]);
        if($userInBdd){
            throw new HttpException(422, sprintf("L'email \"%s\" est déjà utilisé.", $data->getEmail()));
        }

        // check if isset team un bdd
        $team = $this->teamRepository->find($data->getTeam());
        if (!$team) {
            throw new NotFoundHttpException(sprintf("l'Equipe avec l'ID : %d n'existe pas", $data->getTeam()));
        }

        // user table
        $user = new User();
        $user->setFirstName($data->getFirstName());
        $user->setLastName($data->getLastName());
        $user->setPhone($data->getPhone());
        $user->setTeam($team);

        // user_auth table
        $authUser = new AuthUser();

        $authUser->setEmail($data->getEmail());

        // generate rand password and hash this
        $randPass= $this->passwordGenerator->generatePassword(8);
        $hashedPassword = $this->passwordHasher->hashPassword($authUser,$randPass);
        $authUser->setPassword($hashedPassword);
        
        $authUser->setRoles($data->getRole());

        $user->setAuthUser($authUser);

        $this->entityManager->persist($user);
        $this->entityManager->persist($authUser);
        $this->entityManager->flush();

        return new UserDetailDto(
            $user->getId(),
            $user->getFirstName(),
            $user->getLastName(),
            $user->getCreatedAt(),
            $user->getUpdatedAt(),
            $user->getPhone(),
            $team->getName(),
            $authUser ? $authUser->getEmail() : null,
            $authUser ? $authUser->getRoles() : null,
        );
    }

    private function handleUpdate(int $userId,UserUpdateDto $data): UserDetailDto
    {
        // Récupération de l'utilisateur existant
        $user = $this->userRepository->find($userId);
        
        if (!$user) {
            throw new NotFoundHttpException(sprintf("L'utilisateur avec l'ID : %d n'existe pas", $userId));
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
            $hashedPassword = $this->passwordHasher->hashPassword($authUser,$data->getPassword());
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