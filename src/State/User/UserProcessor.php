<?php

namespace App\State\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use ApiPlatform\Metadata\Delete;
# Dt0
use App\Dto\User\UserCreateDto;
use App\Dto\User\UserUpdateDto;
use App\Dto\User\UserDetailDto;
use App\Dto\User\UserDetailUpdateDto;
# Entity
use App\Entity\User;
use App\Entity\AuthUser;
use App\Entity\Notification;
# Repository
use App\Repository\TeamRepository;
use App\Repository\AuthUserRepository;
use App\Repository\UserRepository;
# Service
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Service\PasswordGenerator;
use Doctrine\ORM\EntityManagerInterface;
#Email
use Symfony\Component\Messenger\MessageBusInterface;
use App\Message\MessageHandler\RegisterNewUser\RegisterNewUserData;

class UserProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PasswordGenerator $passwordGenerator,
        private UserPasswordHasherInterface $passwordHasher,
        private UserRepository $userRepository,
        private TeamRepository $teamRepository,
        private AuthUserRepository $authUserRepository,
        private MessageBusInterface $messageBus,
        private ValidatorInterface $validator
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($data instanceof UserCreateDto) {
            return $this->handleCreate($data);
        }
        
        if ($data instanceof UserUpdateDto) {
            $userId = $uriVariables['id'] ?? null;
            return $this->handleUpdate($userId,$data);
        }

        if ($operation instanceof Delete && $data instanceof User) {
            $this->handleDelete($data);
            return null;
        }

        return $data;
    }

    private function handleCreate(UserCreateDto $data): UserDetailDto
    {   
        $teamId=$data->getTeam();
        $team = $this->teamRepository->find($teamId);
        if(!$team){
            throw new NotFoundHttpException("L'équipe avec l'ID ".$data->getTeam()." n'existe pas");
        }
        // user table
        $user = new User();
        $user->setFirstName($data->getFirstName());
        $user->setLastName($data->getLastName());
        $user->setPhone($data->getPhone());
        $user->setStatus(1);
        $user->setTeam($team);

        // user_auth table
        $authUser = new AuthUser();
        $authUser->setEmail($data->getEmail());
        // generate rand password and hash this
        $randPass= $this->passwordGenerator->generatePassword();
        $hashedPassword = $this->passwordHasher->hashPassword($authUser,$randPass);
        $authUser->setPassword($hashedPassword);
        $authUser->setRoles($data->getRole());

        $user->setAuthUser($authUser);

        $notificationUser = new Notification();
        $notificationUser->setText("Bonjour ".$user->getFirstName()." ".$user->getLastName()." Bienvenue dans l'équipe ".$team->getName()." !");
        $notificationUser->setUser($user);

        $this->entityManager->persist($user);
        $this->entityManager->persist($notificationUser);
        $this->entityManager->flush();

        // asynchrone send Email
        $this->messageBus->dispatch(new RegisterNewUserData($data->getEmail(), $randPass));

        

        // return new user
        return new UserDetailDto(
            $user->getId(),
            $user->getFirstName(),
            $user->getLastName(),
            $user->getCreatedAt(),
            $user->getUpdatedAt(),
            $user->getPhone(),
            $user->getStatus(),
            $user->getTeam()?->getId(),
            $user->getTeam()?->getName(),
            $user->getAuthUser()->getEmail(),
            $user->getAuthUser()->getRoles(),
        );
    }

    private function handleUpdate(int $userId,UserUpdateDto $data): UserDetailUpdateDto
    {
        
        $user = $this->userRepository->find($userId);
        
        if (!$user) {
            throw new NotFoundHttpException(sprintf("L'utilisateur avec l'ID : %d n'existe pas", $userId));
        }

        $authUser = $user->getAuthUser();

        // data optional
        if ($data->getFirstName() !== null) {
            $user->setFirstName($data->getFirstName());
        }

        if ($data->getLastName() !== null) {
            $user->setLastName($data->getLastName());
        }

        if ($data->getEmail() !== null) {
            $authUser->setEmail($data->getEmail());
        }

        if ($data->getPhone() !== null) {
            $user->setPhone($data->getPhone());
        }

        if ($data->getStatus() !== null) {
            $user->setStatus($data->getStatus());
        }
        
        if ($data->getRole() !== null) {
            $authUser->setRoles($data->getRole());
        }

        if ($data->getTeam() !== null) {
            $teamId=$data->getTeam();
            $team = $this->teamRepository->find($teamId);
            if(!$team){
                throw new NotFoundHttpException("L'équipe avec l'ID " . $data->getTeam() . " n'existe pas");
            }
            $user->setTeam($team);
        }

        $notificationUser = new Notification();
        $notificationUser->setText("Votre profil à était mis à jour");
        $notificationUser->setUser($user);
        
        $this->entityManager->persist($notificationUser);
        $this->entityManager->flush();

        // return new user
        return new UserDetailUpdateDto(
            $user->getId(),
            $user->getFirstName(),
            $user->getLastName(),
            $user->getPhone(),
            $user->getTeam()?->getId(),
            $user->getTeam()?->getName(),
            $user->getStatus(),
            $user->getAuthUser()->getEmail(),
            $user->getAuthUser()->getRoles()
        );
    }

    private function handleDelete(User $user){

        $auth = $user->getAuthUser();

        if ($auth) {
            $this->entityManager->remove($auth);
        }

        $user->setIsDeleted(true);

        $teamManager= $this->teamRepository->findTeamManager($user->getTeam()->getId());
        $notificationTeamManger = new Notification();
        $notificationTeamManger->setText($user->getFirstName()." ".$user->getLastName()." ne fait plus partie de l'équipe.");
        $notificationTeamManger->setUser($teamManager);

        $this->entityManager->flush();

        return null;
    }

}