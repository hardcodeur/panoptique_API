<?php

namespace App\Dto\Shift;

use ApiPlatform\Metadata\ApiProperty;
use App\Dto\Shift\ShiftUserDto;

final class ShiftDetailDto
{

    private ?\IntlDateFormatter $dateFormatter = null;
    private const HOUR_FORMAT = "H\\hi";

    public function __construct(
        #[ApiProperty(identifier: true)]
        private int $id,
        private ?\DateTimeImmutable $start = null,
        private ?\DateTimeImmutable $end = null,
        private ?string $activity = null,
        private array $users = []
    ) {
    }

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

     public function getId(): int
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

    public function getActivity(): ?string
    {
        return $this->activity;
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

    public function getEndHourFormat(): ?string
    {
        $date = $this->end->setTimezone(new \DateTimeZone('Europe/Paris'));
        return $date->format(self::HOUR_FORMAT);
    }

    /**
     * @return ShiftUserDto[]
     */
    public function getUsers(): array
    {
        return $this->users;
    }
}