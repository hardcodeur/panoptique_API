<?php

namespace App\Dto\Team;
use ApiPlatform\Metadata\ApiProperty;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO for creating a new Team via API
 */
class TeamUpdateDto
{   
    public function __construct(
        
        #[ApiProperty(identifier: true)]
        private ?int $id = null,
        
        #[Assert\Length(
            min: 2,
            max: 50,
            minMessage: 'Le nom doit contenir au moins {{ limit }} caractères',
            maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères'
        )]
        private ?string $teamName = null,
        
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTeamName(): ?string
    {
        return $this->teamName;
    }
    
}