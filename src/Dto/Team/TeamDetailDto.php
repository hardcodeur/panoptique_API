<?php

namespace App\Dto\Team;

use ApiPlatform\Metadata\ApiProperty;

class TeamDetailDto
{
    public function __construct(
        
        #[ApiProperty(identifier: true)]
        private ?int $id = null,
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