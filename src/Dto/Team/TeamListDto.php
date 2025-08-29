<?php
namespace App\Dto\Team;

use ApiPlatform\Metadata\ApiProperty;

/**
 * DTO for User list representation in API responses
 */
class TeamListDto
{
    public function __construct(
        #[ApiProperty(identifier: true)]
        private ?string $id = null,
        
        private ?string $teamName = null,

    ) {
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getTeamName(): ?string
    {
        return $this->teamName;
    }
    
}                                                                                                               