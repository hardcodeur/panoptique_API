<?php

namespace App\Dto\User;

use ApiPlatform\Metadata\ApiProperty;

/**
 * DTO for detailed User representation in API responses
 */
class UserDetailDto
{
    public function __construct(
        
        #[ApiProperty(identifier: true)]
        private ?int $id = null,
        
        private ?string $firstName = null,
        
        private ?string $lastName = null,
        
        private ?\DateTimeImmutable $createdAt = null,
        
        private ?\DateTimeImmutable $updatedAt = null,
        
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
    
    public function getEmail(): ?string
    {
        return $this->email;
    }
    
    public function getRoles(): array
    {
        return $this->roles;
    }
    
    public function getLastLogin(): ?\DateTimeImmutable
    {
        return $this->lastLogin;
    }
}

