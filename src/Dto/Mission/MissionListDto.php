<?php

namespace App\Dto\Mission;

use ApiPlatform\Metadata\ApiProperty;

/**
 * DTO for detailed  Profil representation in API responses
 */
class MissionListDto
{
    public function __construct(
        #[ApiProperty(identifier: true)]
        private ?int $id = null,
        private ?\DateTimeImmutable $start = null,
        private ?\DateTimeImmutable $end = null,
        private ?string $customer = null,
        private ?string $product = null,
        private ?string $location = null,
        private ?string $address = null,
        private ?string $team = null,
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

    public function getEnd(): ?\DateTimeImmutable
    {
        return $this->end;
    }

    public function getCustomer(): ?string
    {
        return $this->customer;
    }

    // public function getDuration(): ?string
    // {
    //     return $this->duration;
    // }

    public function getProduct(): ?string
    {
        return $this->product;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function getTeam(): ?string
    {
        return $this->team;
    }
}

