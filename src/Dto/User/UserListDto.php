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

        private ?int $teamId = null,

        private ?string $teamName = null,

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

    public function getTeamId(): ?int
    {
        return $this->teamId;
    }

    public function getTeamName(): ?string
    {
        return $this->teamName;
    }

    public function getStatus(): ?string
    {   
        
        return $this->status;
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
        $roleNormelize = strtolower(str_replace('ROLE_','', $role));
        if($roleNormelize === "user" ){
            return "agent";
        }
        return $roleNormelize;    
    }


}