<?php

namespace App\Dto\UpdateCheker;

use ApiPlatform\Metadata\ApiProperty;
use App\Dto\UpdateCheker\MissionShiftUserUpdateCheckerDto;

class MissionShiftUpdateCheckerDto{

        public function __construct(
        #[ApiProperty(identifier: true)]
        private int $id,
        private ?\DateTimeImmutable $start = null,
        private ?\DateTimeImmutable $end = null,
        private ?string $activity = null,
        private array $users = []
    ) {
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

    /**
     * @return MissionShiftUserUpdateCheckerDto[]
    */
    public function getUsers(): array
    {
        return $this->users;
    }
}