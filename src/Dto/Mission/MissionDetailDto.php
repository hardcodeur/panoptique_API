<?php

namespace App\Dto\Mission;

use ApiPlatform\Metadata\ApiProperty;

/**
 * DTO for detailed User representation in API responses
 */
class MissionDetailDto
{   

    private const DATE_FORMAT = "d/m/Y H:i:s";

    public function __construct(
        
        #[ApiProperty(identifier: true)]
        private ?int $id = null,
        
        private ?\DateTimeImmutable $start = null,
        
        private ?\DateTimeImmutable $end = null,

        private ?string $customer = null,

        private ?string $team = null,
        
        private ?\DateTimeImmutable $createdAt = null,
        
        private ?\DateTimeImmutable $updatedAt = null
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStart(): ?string
    {
        $date = $this->start->setTimezone(new \DateTimeZone('Europe/Paris'));
        return $date->format(self::DATE_FORMAT);
    }

    public function getEnd(): ?string
    {
        $date = $this->end->setTimezone(new \DateTimeZone('Europe/Paris'));
        return $date->format(self::DATE_FORMAT);
    }

    public function getCustomer(): ?string
    {
        return $this->customer;
    }

    public function getTeam(): ?string
    {
        return $this->team;
    }
    
    public function getCreatedAt(): ?string
    {
        $date = $this->createdAt->setTimezone(new \DateTimeZone('Europe/Paris'));
        return $date->format(self::DATE_FORMAT);
    }
    
    public function getUpdatedAt(): ?string
    {
        if ($this->updatedAt === null) {
            return $this->updatedAt;
        }

        $date = $this->updatedAt->setTimezone(new \DateTimeZone('Europe/Paris'));
        return $date->format(self::DATE_FORMAT);
    }
}