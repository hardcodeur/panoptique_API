<?php

namespace App\Message\MessageHandler\RegisterUpdateMissionWithShift;

use App\Message\Email\MissionEmail;
use App\Message\MessageHandler\RegisterUpdateMissionWithShift\RegisterUpdateMissionWithShiftData;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SendRegisterUpdateMissionWithShiftMessageHandler
{
    public function __construct(
        private MissionEmail $email,
        private LoggerInterface $logger
    ) {
    }

    public function __invoke(RegisterUpdateMissionWithShiftData $data): void
    {
        try {
             $this->email->registreUpdateMissionWithShift($data->getUserEmail(),$data->getMission());
             sleep(2);
        } catch (\Exception $e) {
            $this->logger->error(
                "Échec de l'envoi de l'email via le handler SendRegisterUpdateMissionWithShiftMessageHandler",
                [
                    'error_message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]
            );
            throw $e;
        }
    }
}
