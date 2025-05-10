<?php

namespace App\Dto\UserShifts;

final class ShiftDto
{   
    public ?\IntlDateFormatter $dateFormatter = null;

    public function __construct(
        public \DateTimeImmutable $shiftStart,
        public \DateTimeImmutable $shiftEnd,
        public string $activity,
        public string $missionId,
    ) {
    }

    public function getShiftStartDateFormat(): ?string
    {
        $date = $this->shiftStart->setTimezone(new \DateTimeZone('Europe/Paris'));
        return $this->getDateFormatter()->format($date);
    }

    public function getShiftStartHourFormat(): ?string
    {
        $date = $this->shiftStart->setTimezone(new \DateTimeZone('Europe/Paris'))->format("H\hi");
        return $date;
    }

    public function getShiftEndHourFormat(): ?string
    {
        $date = $this->shiftEnd->setTimezone(new \DateTimeZone('Europe/Paris'))->format("H\hi");
        return $date;
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
}