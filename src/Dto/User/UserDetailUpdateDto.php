<?php

namespace App\Dto\User;

use ApiPlatform\Metadata\ApiProperty;

/**
 * DTO for detailed User representation in API responses
 */
class UserDetailUpdateDto
{   

    public function __construct(

        #[ApiProperty(identifier: true)]
        private ?int $id = null,
        
        private ?string $firstName = null,

        private ?string $lastName = null,

        private ?string $phone = null,

        private ?string $team = null,

        private ?string $teamName = null,

        private ?string $status = null,
        
        private ?string $email = null,
        
        private ?array $role = [],

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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getTeam(): ?string
    {
        return $this->team;
    }

    public function getTeamName(): ?string
    {
        return $this->teamName;
    }

    public function getStatus(): ?string
    {   
        
        return $this->status;
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
