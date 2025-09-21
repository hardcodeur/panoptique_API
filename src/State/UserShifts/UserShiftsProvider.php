<?php

namespace App\State\UserShifts;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\UserShifts\ShiftDto;
use App\Dto\UserShifts\UserShiftsOutputDto;
use App\Repository\ShiftRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class UserShiftsProvider implements ProviderInterface
{
    public function __construct(
        private ShiftRepository $shiftRepository,
        private Security $security
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
                
        /** @var \App\Entity\AuthUser|null $authUser */
        $authUser = $this->security->getUser();

        if (!$authUser) {
            throw new UnauthorizedHttpException('Bearer', 'User not authenticated.');
        }
        
        $user = $authUser->getUser();

        $shifts = $this->shiftRepository->findUserShiftsForCurrentWeek($user->getId());

        $shiftDtos = array_map(
            fn ($shift) => new ShiftDto(
                 $shift->getId(),
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

