<?php
namespace App\Dto\User;

use ApiPlatform\Metadata\ApiProperty;

/**
 * DTO for User list representation in API responses
 */
class UserListDto
{
    public function __construct(
        #[ApiProperty(identifier: true)]
        private ?int $id = null,
        
        private ?string $firstName = null,
        
        private ?string $lastName = null,
        
        private ?\DateTimeImmutable $createdAt = null,
        
        private ?string $email = null,
        
        private ?string $roleName = null
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
    
    public function getEmail(): ?string
    {
        return $this->email;
    }
    
    public function getRoleName(): ?string
    {
        return $this->roleName;
    }
}