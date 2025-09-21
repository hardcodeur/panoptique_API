<?php

namespace App\Message\MessageHandler\RegisterNewMissionWithShift;

use App\Entity\Mission;

final class RegisterNewMissionWithShiftData
{
    public function __construct(
        private string $userEmail,
        private Mission $mission
    ) {
    }

    public function getUserEmail(): string
    {
        return $this->userEmail;
    }

    public function getMission(): Mission
    {
        return $this->mission;
    }
}