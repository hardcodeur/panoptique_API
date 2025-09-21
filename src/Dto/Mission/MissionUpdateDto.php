<?php

namespace App\Dto\Mission;

use Symfony\Component\Validator\Constraints as Assert;
use App\Dto\Shift\ShiftUpdateDto;

/**
 * DTO for updating a Mission via API
 */
class MissionUpdateDto
{
    public function __construct(

        #[Assert\Type(type: "\DateTimeImmutable", message: "Le format de la date est invalide")]
        private ?\DateTimeImmutable $start = null,

        #[Assert\Type(type: "\DateTimeImmutable", message: "Le format de la date est invalide")]
        #[Assert\GreaterThan(propertyPath: "start", message: "La date de fin doit être supérieure à la date de début")]
        private ?\DateTimeImmutable $end = null,

        /** @var ShiftUpdateDto[] */
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

    /**
     * @return ShiftUpdateDto[]
    */
    public function getShifts(): array
    {
        return $this->shifts;
    }

    /**
     * @param ShiftUpdateDto[]
     */
    public function setShifts(array $shifts): self
    {
        $this->shifts = $shifts;
        return $this;
    }
}
