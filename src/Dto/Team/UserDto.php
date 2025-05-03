<?php

namespace App\Dto\Team;


class UserDto
{
    public function __construct(
        private ?int $id = null,
        private ?string $fullname = null,
        private ?array $role = null,
        private ?string $status = null,
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullname(): ?string
    {
        return $this->fullname;
    }

    public function getStatus(): ?string
    {   
        
        return $this->status;
    }

    public function getRole(): ?string
    {   
        $role = $this->role[0];
        return strtolower(str_replace('ROLE_', '', $role));
    }
}