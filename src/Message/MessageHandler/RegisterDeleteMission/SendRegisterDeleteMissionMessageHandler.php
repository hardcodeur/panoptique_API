<?php

namespace App\Message\MessageHandler\RegisterDeleteMission;

use App\Message\Email\MissionEmail;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SendRegisterDeleteMissionMessageHandler
{
    public function __construct(
        private MissionEmail $email,
        private LoggerInterface $logger
    ) {
    }

    public function __invoke(RegisterDeleteMissionData $data): void
    {
        try {
            $this->email->registreDeleteMission($data->getUserEmail(),$data->getMission());
        } catch (\Exception $e) {
            $this->logger->error(
                "Échec de l'envoi de l'email via le handler SendRegisterNewMissionMessageHandler",
                [
                    'error_message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]
            );
            throw $e;
        }
    }
}