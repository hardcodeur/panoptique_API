<?php

namespace App\Message\MessageHandler\RegisterNewMission;

use App\Entity\Mission;

final class RegisterNewMissionData
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
