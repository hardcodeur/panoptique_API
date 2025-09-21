<?php

namespace App\Message\MessageHandler\RegisterNewMission;

use App\Message\Email\MissionEmail;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SendRegisterNewMissionMessageHandler
{
    public function __construct(
        private MissionEmail $email,
        private LoggerInterface $logger
    ) {
    }

    public function __invoke(RegisterNewMissionData $data): void
    {
        try {
             $this->email->registreNewMission($data->getUserEmail(),$data->getMission());
             sleep(2);
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