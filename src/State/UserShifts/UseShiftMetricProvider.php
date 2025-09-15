<?php

namespace App\State\UserShifts;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\UserShifts\UseShiftMetricOutputDto;
use App\Repository\ShiftRepository;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class UseShiftMetricProvider implements ProviderInterface
{
    public function __construct(
        private ShiftRepository $shiftRepository,
        private Security $security,
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
        $data = $this->shiftRepository->findUseShiftMetric($user->getId());
    
        return new UseShiftMetricOutputDto(
            date('F Y', strtotime('first day of this month')),
            (float) $data['totalHours'],
            (int) $data['totalShifts'],
            $data['activitiesCount']
        );
    }
}