<?php

namespace App\Dto\Shift;

final class ShiftUserDto
{
    public function __construct(
        private ?int $id = null,
        private ?string $userFullname = null,
        private ?string $userRole = null
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserFullname(): ?string
    {
        return $this->userFullname;
    }

    public function getUserRole(): ?string
    {
        return $this->userRole;
    }
}
