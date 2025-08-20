<?php

namespace App\Dto\User;

use ApiPlatform\Metadata\ApiProperty;

/**
 * DTO for detailed User representation in API responses
 */
class UserDetailDto
{   

    private const DATE_FORMAT = "d/m/Y H:i:s";

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
        
        private array $role = [],

        
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
        
    public function getCreatedAt(): ?string
    {
        $date = $this->createdAt->setTimezone(new \DateTimeZone('Europe/Paris'));
        return $date->format(self::DATE_FORMAT);
    }
    
    public function getUpdatedAt(): ?string
    {
        if ($this->updatedAt === null) {
            return $this->updatedAt;
        }

        $date = $this->updatedAt->setTimezone(new \DateTimeZone('Europe/Paris'));
        return $date->format(self::DATE_FORMAT);
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }
    
    public function getEmail(): ?string
    {
        return $this->email;
    }
    
    public function getRole(): string
    {
        $role = $this->role[0];
        $roleNormelize = strtolower(str_replace('ROLE_','', $role));
        if($roleNormelize === "user" ){
            return "agent";
        }
        return $roleNormelize;
    }

    public function getTeam(): ?string
    {
        return $this->team;
    }
    
    public function getLastLogin(): ?string
    {
        if ($this->lastLogin === null) {
            return $this->lastLogin;
        }

        $date = $this->lastLogin->setTimezone(new \DateTimeZone('Europe/Paris'));
        return $date->format(self::DATE_FORMAT);
    }
}

