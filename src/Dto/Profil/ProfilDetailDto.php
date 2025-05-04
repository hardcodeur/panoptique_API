<?php

namespace App\Dto\Profil;

use ApiPlatform\Metadata\ApiProperty;

/**
 * DTO for detailed  Profil representation in API responses
 */
class ProfilDetailDto
{
    public function __construct(
        
        #[ApiProperty(identifier: true)]
        private ?int $id = null,
        
        private ?string $firstName = null,
        
        private ?string $lastName = null,
        
        private ?\DateTimeImmutable $createdAt = null,
        
        private ?\DateTimeImmutable $updatedAt = null,

        private ?string $phone = null,
        
        private ?string $team = null,
        
        private ?string $email = null,
        
        private array $roles = [],

        
        private ?\DateTimeImmutable $lastLogin = null
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }
    
    public function getLastName(): ?string
    {
        return $this->lastName;
    }
        
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }
    
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }
    
    public function getEmail(): ?string
    {
        return $this->email;
    }
    
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getTeam(): ?string
    {
        return $this->team;
    }
    
    public function getLastLogin(): ?\DateTimeImmutable
    {
        return $this->lastLogin;
    }
}

