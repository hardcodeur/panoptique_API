<?php

namespace App\Message\MessageHandler\RegisterNewUser;

use App\Message\Email\UserEmail;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SendRegisterNewUserMessageHandler
{
    public function __construct(
        private UserEmail $userEmail,
        private LoggerInterface $logger
    ) {
    }

    public function __invoke(RegisterNewUserData $data): void
    {
        try {
            $this->userEmail->registreNewUser($data->getUserEmail(),$data->getGeneratedPassword());
        } catch (\Exception $e) {
            $this->logger->error(
                "Échec de l'envoi de l'email via le handler SendRegisterNewUserMessageHandler",
                [
                    'error_message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]
            );
            throw $e;
        }
    }
}
