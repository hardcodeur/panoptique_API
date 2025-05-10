<?php

namespace App\State\UserShifts;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\UserShifts\ShiftDto;
use App\Dto\UserShifts\UserShiftsOutputDto;
use App\Repository\ShiftRepository;

class UserShiftsProvider implements ProviderInterface
{
    public function __construct(
        private ShiftRepository $shiftRepository
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $userId = $uriVariables['userId'];
        $shifts = $this->shiftRepository->findUserShiftsForCurrentWeek($userId);

        $shiftDtos = array_map(
            fn ($shift) => new ShiftDto(
                $shift->getStart(),
                $shift->getEnd(),
                $shift->getActivity(),
                $shift->getMission()->getId()
            ),
            $shifts
        );

        return new UserShiftsOutputDto($shiftDtos);
    }
}

