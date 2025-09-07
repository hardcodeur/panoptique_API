<?php

namespace App\Dto\UpdateCheker;

use ApiPlatform\Metadata\ApiProperty;

class UserUpdateCheckerDto
{
    public function __construct(
        #[ApiProperty(identifier: true)]
        private ?int $id = null,
        private ?string $firstName = null,
        private ?string $lastName = null,
        private ?string $email = null,
        private ?string $phone = null,
        private ?string $status = null,
        private ?string $role = null,
        private ?string $team = null
    ) {
    }

    private const ROLE = [
        "ROLE_ADMIN"=>"admin",
        "ROLE_MANAGER"=>"manager",
        "ROLE_TEAM_MANAGER"=>"team_manager",
        "ROLE_USER"=>"agent"
    ];

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }
    
    public function getLastName(): ?string
    {
        return $this->lastName;
    }
    
    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }
        
    public function getRole(): ?string
    {
        return self::ROLE[$this->role];
    }

    public function getTeam(): ?string
    {
        return $this->team;
    }
}