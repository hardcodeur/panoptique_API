<?php

namespace App\Dto\Shift;

use ApiPlatform\Metadata\ApiProperty;


class ShiftDto
{   

    private const HOUR_FORMAT = "H\hi";

    public function __construct(
        #[ApiProperty(identifier: true)]
        private ?int $id = null,
        private ?\DateTimeImmutable $start = null,
        private ?\DateTimeImmutable $end = null,
        private ?string $activity = null,
        private ?string $userFullname = null,
        private ?array $userRole = null,
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

    public function getStartHourFormat(): ?string
    {
        $date = $this->start->setTimezone(new \DateTimeZone('Europe/Paris'));
        return $date->format(self::HOUR_FORMAT);
    }

    public function getEnd(): ?\DateTimeImmutable
    {
        return $this->end;
    }
    
    public function getEndHourFormat(): ?string
    {
        $date = $this->end->setTimezone(new \DateTimeZone('Europe/Paris'));
        return $date->format(self::HOUR_FORMAT);
    }

    public function getUserFullname(): ?string
    {
        return $this->userFullname;
    }

    public function getActivity(): ?string
    {   
        
        return $this->activity;
    }

    public function getUserRole(): ?string
    {   
        $role = $this->userRole[0];
        return strtolower(str_replace('ROLE_', '', $role));
    }
}