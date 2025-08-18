<?php

namespace App\Message\MessageHandler\RegisterNewUser;

final class RegisterNewUserData
{
    public function __construct(
        private string $userEmail,
        private string $generatedPassword
    ) {
    }

    public function getUserEmail(): string
    {
        return $this->userEmail;
    }

    public function getGeneratedPassword(): string
    {
        return $this->generatedPassword;
    }
}
