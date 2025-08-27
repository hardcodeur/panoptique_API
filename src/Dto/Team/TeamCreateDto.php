<?php

namespace App\Dto\Team;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO for creating a new Team via API
 */
class TeamCreateDto
{
    public function __construct(
        #[Assert\NotBlank(message: "Le nom de l'équipe est obligatoire")]
        #[Assert\Length(
            min: 2,
            max: 50,
            minMessage: 'Le nom doit contenir au moins {{ limit }} caractères',
            maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères'
        )]
        private ?string $teamName = null,
        
    ) {
    }

    public function getTeamName(): ?string
    {
        return $this->teamName;
    }
    
}