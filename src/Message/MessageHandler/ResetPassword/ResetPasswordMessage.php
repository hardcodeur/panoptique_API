<?php

namespace App\Message\MessageHandler\ResetPassword;

final class ResetPasswordMessage
{
    public function __construct(
        private readonly string $email,
        private readonly string $newPassword
    ) {
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getNewPassword(): string
    {
        return $this->newPassword;
    }
}
