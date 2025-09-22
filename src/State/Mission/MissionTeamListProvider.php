<?php

namespace App\State\Mission;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Bundle\SecurityBundle\Security;

use App\Dto\Mission\MissionListDto;
use App\Repository\MissionRepository;

class MissionTeamListProvider implements ProviderInterface
{   
    public function __construct(
        private MissionRepository $missionRepository,
        private Security $security
    ) {
    }

    private ?\IntlDateFormatter $dateFormatter = null;

    private function getDateFormatter(): \IntlDateFormatter
    {
        if ($this->dateFormatter === null) {
            $this->dateFormatter = new \IntlDateFormatter(
                'fr_FR',
                \IntlDateFormatter::FULL,
                \IntlDateFormatter::NONE,
                'Europe/Paris',
                \IntlDateFormatter::GREGORIAN,
                'EEEE d MMMM' // format "lundi 6 mai"
            );
        }
        
        return $this->dateFormatter;
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        /** @var \App\Entity\AuthUser|null $authUser */
        $authUser = $this->security->getUser();

        if (!$authUser) {
            throw new UnauthorizedHttpException('Bearer', 'User not authenticated.');
        }

        $user = $authUser->getUser();
        
        $missions = $this->missionRepository->findCurrentAndFutureMissionsTeamByTeamId($user->getTeam()->getId());

        return array_map(function ($mission) {
            
            $shiftsByActivityAndTime = [
                'connexion' => [],
                'surveillance' => [],
                'deconnexion' => []
            ];
            
            // regrouper les shifts par activité et créneau horaire
            foreach ($mission->getShifts() as $shift) {
                $user = $shift->getUser();
                $authUser = $user->getAuthUser();
                $role= $authUser->getRoles();
                
                $timeKey = $shift->getStart()->format('U').'-'.$shift->getEnd()->format('U');
                $activity = $shift->getActivity();
                $date = $shift->getStart()->setTimezone(new \DateTimeZone('Europe/Paris'));
                
                if (!isset($shiftsByActivityAndTime[$activity][$timeKey])) {
                    $shiftsByActivityAndTime[$activity][$timeKey] = [
                        'id'=>$shift->getId(),
                        'start' => $shift->getStart(),
                        'end' => $shift->getEnd(),
                        'activity' => $shift->getActivity(),
                        'startDateFormat' => $this->getDateFormatter()->format($date),
                        'startHourFormat' => $shift->getStart()->format('H\hi'),
                        'endHourFormat' => $shift->getEnd()->format('H\hi'),
                        'users' => []
                    ];
                }
                
                $shiftsByActivityAndTime[$activity][$timeKey]['users'][] = [
                    'id' => $user->getId(),
                    'userFullname' => $user->getFirstName().' '.$user->getLastName(),
                    'userRole' => strtolower(str_replace('ROLE_', '', $role[0])),
                ];
            }
            
            // Deuxième passage : réorganiser dans la structure finale
            $shiftsDto = [];
            
            foreach ($shiftsByActivityAndTime as $activity => $timeSlots) {
                foreach ($timeSlots as $timeSlot) {
                    $shiftsDto[] = $timeSlot;
                }
            }
            
            return new MissionListDto(
                $mission->getId(),
                $mission->getStart(),
                $mission->getEnd(),
                $mission->getCustomer()->getId(),
                $mission->getCustomer()->getName(),
                $mission->getCustomer()->getProduct(),
                $mission->getCustomer()->getLocation()->getName(),
                $mission->getCustomer()->getLocation()->getAddress(),
                $mission->getTeam()?->getId(),
                $mission->getTeam()?->getName(), 
                $shiftsDto
            );
        }, $missions);
    }
}