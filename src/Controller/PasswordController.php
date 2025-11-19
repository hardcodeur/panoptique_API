<?php

namespace App\Controller;

use App\Entity\RefreshToken;
use App\Message\MessageHandler\ResetPassword\ResetPasswordMessage;
use App\Service\PasswordGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

use App\Dto\Profil\ProfilChangePass;

final class PasswordController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $userRepository,
        private readonly PasswordGenerator $passwordGenerator,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly MessageBusInterface $bus
    ) {
    }

    #[Route('api/reset/password', name: 'api_reset_password', methods: ['POST'])]
    public function resetPassword(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $id = $data['userId'] ?? null;

        if (!$id) {
            return new JsonResponse(['error' => 'Id not found'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $item = $this->userRepository->find($id);

        if (!$item) {
            throw new NotFoundHttpException("User not found");
        }

        // Generate new password and hash this
        $newPassword = $this->passwordGenerator->generatePassword();
        $hashedPassword = $this->passwordHasher->hashPassword($item->getAuthUser(), $newPassword);
        // Update user password
        $item->getAuthUser()->setPassword($hashedPassword);
        $this->entityManager->persist($item);

        // Delete all refresh tokens
        $refreshTokens = $this->entityManager->getRepository(RefreshToken::class)->findBy(['username' => $item->getAuthUser()->getEmail()]);
        if($refreshTokens){
            foreach($refreshTokens as $refreshToken){
                $this->entityManager->remove($refreshToken);
            }
        }

        $this->entityManager->flush();

        // send email
        $this->bus->dispatch(new ResetPasswordMessage($item->getAuthUser()->getEmail(), $newPassword));

        return new JsonResponse(['message' => 'Password reset'], JsonResponse::HTTP_OK);
    }

    #[Route('api/change/password', name: 'api_change_password', methods: ['POST'])]
    public function changePassword(Request $request,#[MapRequestPayload] ProfilChangePass $dto): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $id = $data['profilId'] ?? null;

        if (!$id) {
            return new JsonResponse(['error' => 'Id not found'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $item = $this->userRepository->find($id);

        if (!$item) {
            throw new NotFoundHttpException("User not found");
        }

        $hashedPassword = $this->passwordHasher->hashPassword($item->getAuthUser(), $dto->getNewPass());
        $item->getAuthUser()->setPassword($hashedPassword);

        // Delete all refresh tokens
        $refreshTokens = $this->entityManager->getRepository(RefreshToken::class)->findBy(['username' => $item->getAuthUser()->getEmail()]);
        if($refreshTokens){
            foreach($refreshTokens as $refreshToken){
                $this->entityManager->remove($refreshToken);
            }
        }
        
        $this->entityManager->flush();

        // // send email
        // $this->bus->dispatch(new ResetPasswordMessage($item->getAuthUser()->getEmail(), $newPassword));

        return new JsonResponse(['message' => 'Password update'], JsonResponse::HTTP_OK);
    }
}