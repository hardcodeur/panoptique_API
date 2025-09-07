<?php

namespace App\Dto\UpdateCheker;

use ApiPlatform\Metadata\ApiProperty;

class MissionUpdateCheckerDto
{
    public function __construct(
        #[ApiProperty(identifier: true)]
        private ?int $id = null,
        private ?\DateTimeImmutable $start = null,
        private ?\DateTimeImmutable $end = null,
    ) {
    }

    const DATE_FORMAT = "Y-m-d\TH:i:s.v\Z";

    public function getStart(): ?string
    {
        return $this->start->format(self::DATE_FORMAT);
    }

    public function getEnd(): ?string
    {
        return $this->end->format(self::DATE_FORMAT);
    }

}