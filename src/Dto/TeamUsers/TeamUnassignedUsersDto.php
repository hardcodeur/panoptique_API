<?php

namespace App\Dto\TeamUsers;

use ApiPlatform\Metadata\ApiProperty;

/**
 * DTO for detailed User representation in API responses
 */
class TeamUnassignedUsersDto
{
    public function __construct(
        
        #[ApiProperty(identifier: true)]
        private ?int $id = null,
        
        private ?string $fullName = null,
        
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getFullName(): ?string
    {
        return $this->fullName;
    }
     
}

