<?php

namespace App\Dto\UpdateCheker;

use ApiPlatform\Metadata\ApiProperty;

class TeamUpdateCheckerDto
{
    public function __construct(
        #[ApiProperty(identifier: true)]
        private ?int $id = null,
        private ?string $teamName = null,
    ) {
    }

    public function getTeamName(): ?string
    {
        return $this->teamName;
    }
    
}