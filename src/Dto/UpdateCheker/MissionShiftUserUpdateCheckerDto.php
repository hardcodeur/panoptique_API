<?php

namespace App\Dto\UpdateCheker;

use ApiPlatform\Metadata\ApiProperty;

class MissionShiftUserUpdateCheckerDto{
    public function __construct(
        #[ApiProperty(identifier: true)]
        private ?int $id = null,
    ) {
    }

    public function getId(): int{
        return $this->id;
    }
}