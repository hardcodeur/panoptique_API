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
        
        private ?string $fullName = null,

        private ?string $firstName = null,

        private ?string $lastName = null,

        private ?string $phone = null,

        private ?string $team = null,

        private ?int $status = null,
        
        private ?\DateTimeImmutable $createdAt = null,
        
        private ?string $email = null,
        
        private ?array $role = null,

    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getfullName(): ?string
    {
        return $this->fullName;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getTeam(): ?string
    {
        return $this->team;
    }

    public function getStatus(): ?string
    {   
        
        return ($this->status) ? "Disponible" : "Indisponible" ;
    }
        
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }
    
    public function getEmail(): ?string
    {
        return $this->email;
    }
    
    public function getRole(): ?string
    {   
        $role = $this->role[0];
        return strtolower(str_replace('ROLE_', '', $role));
    }


}