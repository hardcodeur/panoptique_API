<?php

namespace App\Dto\Location;

use Symfony\Component\Validator\Constraints as Assert;
use App\Dto\LocationNote\LocationNoteCreatDto;

class LocationCreateDto
{
    public function __construct(
        #[Assert\NotBlank(message: "Le nom ne peut pas être vide.")]
        #[Assert\Type("string")]
        #[Assert\Length(min: 2, max: 100, minMessage: "Le nom doit contenir au moins {{ limit }} caractères.", maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères.")]
        private ?string $name = null,

        #[Assert\NotBlank(message: "L'adresse ne peut pas être vide.")]
        #[Assert\Type("string")]
        private ?string $address = null,

        #[Assert\NotBlank(message: "L'équipe ne peut pas être vide.")]
        private ?string $team = null,

        /** @var LocationNoteCreatDto[] */
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
     * @return LocationNoteCreatDto[]
     */
    public function getLocationNote(): array
    {
        return $this->locationNote;
    }

    /**
     * @param LocationNoteCreatDto[] $locationNote
     */
    public function setLocationNote(array $locationNote): self
    {
        $this->locationNote = $locationNote;
        return $this;
    }
}