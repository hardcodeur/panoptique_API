<?php

namespace App\Message\MessageHandler\RegisterDeleteMission;

use App\Entity\Mission;

final class RegisterDeleteMissionData
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
