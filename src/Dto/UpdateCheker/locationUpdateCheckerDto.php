<?php

namespace App\Dto\UpdateCheker;

use ApiPlatform\Metadata\ApiProperty;

class locationUpdateCheckerDto
{
    public function __construct(
        #[ApiProperty(identifier: true)]
        private ?int $id = null,
        private ?string $name = null,
        private ?string $address = null,
        private ?string $team = null,
        private array $locationNote = [],
    ) {
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }


    public function getTeam(): ?string
    {
        return $this->team;
    }

    /**
     * @return locationNoteUpdateCheckerDto[]
     */
    public function getLocationNote(): array
    {
        return $this->locationNote;
    }
    
}