<?php

namespace App\Dto\Mission;

use Symfony\Component\Validator\Constraints as Assert;
use App\Dto\Shift\ShiftCreateDto;

/**
 * DTO for creating a new Mission via API
 */
class MissionCreateDto
{
    public function __construct(
        #[Assert\NotBlank(message: "Le début de la mission est obligatoire")]
        #[Assert\Type(type: "\DateTimeImmutable", message: "Le format de la date est invalide")]
        private ?\DateTimeImmutable $start = null,

        #[Assert\NotBlank(message: "La fin de la mission est obligatoire")]
        #[Assert\Type(type: "\DateTimeImmutable", message: "Le format de la date est invalide")]
        #[Assert\GreaterThan(propertyPath: "start", message: "La date de fin doit être supérieure à la date de début")]
        private ?\DateTimeImmutable $end = null,

        #[Assert\NotBlank(message: "Le client est obligatoire")]
        private ?string $customer = null,

        #[Assert\NotBlank(message: "L'équipe est obligatoire")]
        private ?string $team = null,

        /** @var ShiftCreateDto[] */
        #[Assert\Valid]
        private array $shifts = [],
    ) {
    }

    public function getStart(): ?\DateTimeImmutable
    {
        return $this->start;
    }

    public function setStart(?\DateTimeImmutable $start): self
    {
        $this->start = $start;
        return $this;
    }

    public function getEnd(): ?\DateTimeImmutable
    {
        return $this->end;
    }

    public function setEnd(?\DateTimeImmutable $end): self
    {
        $this->end = $end;
        return $this;
    }

    public function getCustomer(): ?string
    {
        return $this->customer;
    }

    public function setCustomer(?string $customer): self
    {
        $this->customer = $customer;
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
     * @return ShiftCreateDto[]
     */
    public function getShifts(): array
    {
        return $this->shifts;
    }

    /**
     * @param ShiftCreateDto[] $locationNote
     */
    public function setShifts(array $shifts): self
    {
        $this->shifts = $shifts;
        return $this;
    }
}