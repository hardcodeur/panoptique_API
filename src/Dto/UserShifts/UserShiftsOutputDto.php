<?php

namespace App\Dto\UserShifts;

final class UserShiftsOutputDto
{
    /**
     * @param array<ShiftDto> $shifts
     */
    public function __construct(
        public array $shifts
    ) {
    }
}