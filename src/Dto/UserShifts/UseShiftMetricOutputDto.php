<?php

namespace App\Dto\UserShifts;

final class UseShiftMetricOutputDto
{
    /**
     * @param array<string, int> $activitiesCount
     */
    public function __construct(
        public string $month,
        public float $totalHours,
        public int $totalShifts,
        public array $activitiesCount
    ) {
    }
}