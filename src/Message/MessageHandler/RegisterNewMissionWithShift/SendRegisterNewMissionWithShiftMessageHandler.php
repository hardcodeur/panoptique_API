<?php

namespace App\Message\MessageHandler\RegisterNewMissionWithShift;

use App\Message\Email\MissionEmail;
use App\Message\MessageHandler\RegisterNewMissionWithShift\RegisterNewMissionWithShiftData;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SendRegisterNewMissionWithShiftMessageHandler
{
    public function __construct(
        private MissionEmail $email,
        private LoggerInterface $logger
    ) {
    }

    public function __invoke(RegisterNewMissionWithShiftData $data): void
    {
        try {
             $this->email->registreNewMissionWithShift($data->getUserEmail(),$data->getMission());
             sleep(2);
        } catch (\Exception $e) {
            $this->logger->error(
                "Échec de l'envoi de l'email via le handler SendRegisterNewMissionwithShiftMessageHandler",
                [
                    'error_message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]
            );
            throw $e;
        }
    }
}
