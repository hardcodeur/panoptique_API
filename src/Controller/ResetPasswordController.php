<?php

namespace App\Controller;

use App\Entity\User;
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

final class ResetPasswordController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly PasswordGenerator $passwordGenerator,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly MessageBusInterface $bus
    ) {
    }

    #[Route('api/reset/password', name: 'api_reset_password', methods: ['POST'])]
    public function resetPassword(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $userId = $data['userId'] ?? null;

        if (!$userId) {
            return new JsonResponse(['error' => 'Id not found'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $userId]);

        if (!$user) {
            throw new NotFoundHttpException("User not found");
        }

        // Generate new password and hash this
        $newPassword = $this->passwordGenerator->generatePassword();
        $hashedPassword = $this->passwordHasher->hashPassword($user->getAuthUser(), $newPassword);
        // Update user password
        $user->getAuthUser()->setPassword($hashedPassword);
        $this->entityManager->persist($user);

        // Delete all refresh tokens
        $refreshTokens = $this->entityManager->getRepository(RefreshToken::class)->findBy(['username' => $user->getAuthUser()->getEmail()]);
        if($refreshTokens){
            foreach($refreshTokens as $refreshToken){
                $this->entityManager->remove($refreshToken);
            }
        }

        $this->entityManager->flush();

        // send email
        $this->bus->dispatch(new ResetPasswordMessage($user->getAuthUser()->getEmail(), $newPassword));

        return new JsonResponse(['message' => 'Password reset'], JsonResponse::HTTP_OK);
    }
}