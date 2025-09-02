<?php

namespace App\Dto\Mission;

use ApiPlatform\Metadata\ApiProperty;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO for updating a Mission via API
 */
class MissionUpdateDto
{
    public function __construct(
        #[ApiProperty(identifier: true)]
        private ?int $id = null,

        #[Assert\Type(type: "\DateTimeImmutable", message: "Le format de la date est invalide")]
        private ?\DateTimeImmutable $start = null,

        #[Assert\Type(type: "\DateTimeImmutable", message: "Le format de la date est invalide")]
        #[Assert\GreaterThan(propertyPath: "start", message: "La date de fin doit être supérieure à la date de début")]
        private ?\DateTimeImmutable $end = null
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
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
}
