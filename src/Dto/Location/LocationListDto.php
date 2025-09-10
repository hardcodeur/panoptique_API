<?php

namespace App\Dto\Location;

use ApiPlatform\Metadata\ApiProperty;

/**
 * DTO for detailed location representation in API responses
 */
class LocationListDto
{   

    public function __construct(
        #[ApiProperty(identifier: true)]
        private ?int $id = null,
        private ?string $name = null,
        private ?string $address = null,
        private ?int $teamId = null,
        private ?string $team = null,
        /** @var LocationNoteDto[] */
        private array $locationNote = [],
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function getTeamId(): ?string
    {
        return $this->teamId;
    }

    public function getTeam(): ?string
    {
        return $this->team;
    }

    /**
    * @return LocationNoteDto[]
    */
    public function getLocationNote(): array
    {
        return $this->locationNote;
    }

}

