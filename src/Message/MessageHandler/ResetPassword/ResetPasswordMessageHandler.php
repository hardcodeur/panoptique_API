<?php

namespace App\Message\MessageHandler\ResetPassword;

use App\Message\Email\AuthUserEmail;
use App\Message\MessageHandler\ResetPassword\ResetPasswordMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class ResetPasswordMessageHandler
{
    public function __construct(
        private readonly AuthUserEmail $authUserEmail,
        private readonly LoggerInterface $logger
    ) {
    }

    public function __invoke(ResetPasswordMessage $message): void
    {
        try {
            $this->authUserEmail->resetPassword($message->getEmail(), $message->getNewPassword());
            sleep(2);
        } catch (\Exception $e) {
            $this->logger->error(
                "Échec de l'envoi de l'email de réinitialisation via ResetPasswordMessageHandler",
                [
                    'error_message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]
            );
            throw $e;
        }
    }
}
