<?php

namespace App\Dto\Location;

use Symfony\Component\Validator\Constraints as Assert;
use App\Dto\LocationNote\LocationNoteUpdateDto;

class LocationUpdateDto
{
    public function __construct(
        #[Assert\Type("string")]
        #[Assert\Length(min: 2, max: 100, minMessage: "Le nom doit contenir au moins {{ limit }} caractères.", maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères.")]
        private ?string $name = null,

        #[Assert\Type("string")]
        private ?string $address = null,
        private ?string $team = null,

        /** @var LocationNoteUpdateDto[] */
        #[Assert\Valid]
        private array $locationNote = [],
    ) {
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function getTeam(): ?string
    {
        return $this->team;
    }

    public function setTeam(?string $team): self
    {
        $this->team = $team;
        return $this;
    }

    /**
     * @return LocationNoteUpdateDto[]
     */
    public function getLocationNote(): array
    {
        return $this->locationNote;
    }

    /**
     * @param LocationNoteUpdateDto[] $locationNote
     */
    public function setLocationNote(array $locationNote): self
    {
        $this->locationNote = $locationNote;
        return $this;
    }
}