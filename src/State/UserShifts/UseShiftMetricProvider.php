<?php

namespace App\State\UserShifts;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\UserShifts\UseShiftMetricOutputDto;
use App\Repository\ShiftRepository;

class UseShiftMetricProvider implements ProviderInterface
{
    public function __construct(
        private ShiftRepository $shiftRepository
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $userId = $uriVariables['userId'];
        $data = $this->shiftRepository->findUseShiftMetric($userId);
    
        return new UseShiftMetricOutputDto(
            date('F Y', strtotime('first day of this month')),
            (float) $data['totalHours'],
            (int) $data['totalShifts'],
            $data['activitiesCount']
        );
    }
}