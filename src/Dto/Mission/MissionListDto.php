<?php

namespace App\Dto\Mission;

use ApiPlatform\Metadata\ApiProperty;
use App\Dto\Shift\ShiftDetailDto;

/**
 * DTO for detailed Mission representation in API responses
 */
class MissionListDto
{   


    public ?\IntlDateFormatter $dateFormatter = null;
    private const HOUR_FORMAT = "H\hi";

    
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
        private ?int $customerId = null,
        private ?string $customer = null,
        private ?string $product = null,
        private ?string $location = null,
        private ?string $address = null,
        private ?int $teamId = null,
        private ?string $team = null,
        public array $shifts = [],
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

    public function getStartHourFormat(): ?string
    {   
        $date = $this->start->setTimezone(new \DateTimeZone('Europe/Paris'));
        return $date->format(self::HOUR_FORMAT);
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

    public function getEndHourFormat(): ?string
    {   
        $date = $this->end->setTimezone(new \DateTimeZone('Europe/Paris'));
        return $date->format(self::HOUR_FORMAT);
    }

    public function getCustomerId(): ?string
    {
        return $this->customerId;
    }

    public function getCustomer(): ?string
    {
        return $this->customer;
    }

    public function getDuration(): ?string
    {   
        $start = $this->start->setTimezone(new \DateTimeZone('Europe/Paris'));
        $end = $this->end->setTimezone(new \DateTimeZone('Europe/Paris'));
        $interval = $start->diff($end);

        $totalHours = ($interval->days * 24) + $interval->h;
        
        return $totalHours . 'h';
    }

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

    public function getTeamId(): ?string
    {
        return $this->teamId;
    }

    public function getTeam(): ?string
    {
        return $this->team;
    }

    /**
    * @return ShiftDetailDto
    */
    public function getShifts(): array
    {
        return $this->shifts;
    }
}

