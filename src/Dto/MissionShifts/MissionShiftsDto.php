<?php

namespace App\Dto\MissionShifts;

use ApiPlatform\Metadata\ApiProperty;

/**
 * DTO for mission shifts representation in API responses
 */
class MissionShiftsDto
{   
    public ?\IntlDateFormatter $dateFormatter = null;
    private const FULL_FORMAT = "d/m/Y H:i";

    private function getDateFormatter(): \IntlDateFormatter
    {
        if ($this->dateFormatter === null) {
            $this->dateFormatter = new \IntlDateFormatter(
                'fr_FR', 
                \IntlDateFormatter::FULL, 
                \IntlDateFormatter::NONE,
                'Europe/Paris',
                \IntlDateFormatter::GREGORIAN,
                'EEEE d MMMM' // format "lundi 6 mai"
            );
        }
        
        return $this->dateFormatter;
    }

    public function __construct(
        #[ApiProperty(identifier: true)]
        private ?int $id = null,
        private ?\DateTimeImmutable $start = null,
        private ?\DateTimeImmutable $end = null,
        private ?string $location = null,
        private ?string $teamName = null,
        private ?\DateTimeImmutable $createdAt = null,
        private ?\DateTimeImmutable $updatedAt = null,
        private array $shifts = [],
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

    public function getStartDateFormat(): ?string
    {   
        $date = $this->start->setTimezone(new \DateTimeZone('Europe/Paris'));
        return $this->getDateFormatter()->format($date);
    }

    public function getEnd(): ?\DateTimeImmutable
    {
        return $this->end;
    }
    
    public function getEndDateFormat(): ?string
    {
        $date = $this->end->setTimezone(new \DateTimeZone('Europe/Paris'));
        return $this->getDateFormatter()->format($date);
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function getTeamName(): ?string
    {
        return $this->teamName;
    }


    public function getCreatedAt(): ?string
    {   
        if($this->createdAt){
            $date = $this->createdAt->setTimezone(new \DateTimeZone('Europe/Paris'));
            return $date->format(self::FULL_FORMAT);
        }
        
        return $this->createdAt;
    }
    
    public function getUpdatedAt(): ?string
    {   
        if($this->updatedAt){
            $date = $this->updatedAt->setTimezone(new \DateTimeZone('Europe/Paris'));
            return $date->format(self::FULL_FORMAT);
        }

        return $this->updatedAt;
    }

    /**
    * @return ShiftDto[]
    */
    public function getShifts(): array
    {
        return $this->shifts;
    }
    
}  