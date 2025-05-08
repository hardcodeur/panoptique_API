<?php

namespace App\State\MissionShifts;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\MissionShifts\MissionShiftsDto;
use App\Repository\MissionRepository;

class MissionShiftsProvider implements ProviderInterface
{   
    public function __construct(
        private MissionRepository $missionRepository
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $missions = $this->missionRepository->findMissionsWithShifts();
        
        return array_map(function ($mission) {
            // Initialiser la structure des shifts par activité
            $shiftsByActivity = [
                'connexion' => [
                    'startHourFormat' => '',
                    'endHourFormat' => '',
                    'users' => []
                ],
                'surveillance' => [
                    'startHourFormat' => '',
                    'endHourFormat' => '',
                    'users' => []
                ],
                'deconnexion' => [
                    'startHourFormat' => '',
                    'endHourFormat' => '',
                    'users' => []
                ]
            ];
            
            // Remplir les données
            foreach ($mission->getShifts() as $shift) {
                $user = $shift->getUser();
                $authUser = $user->getAuthUser();
                
                $activity = $shift->getActivity();
                
                // Formater les heures (ex: "08h00")
                $startHour = $shift->getStart()->format('H\hi');
                $endHour = $shift->getEnd()->format('H\hi');
                
                // Stocker les heures pour cette activité
                $shiftsByActivity[$activity]['startHourFormat'] = $startHour;
                $shiftsByActivity[$activity]['endHourFormat'] = $endHour;
                
                // Ajouter l'utilisateur
                $role = $authUser->getRoles();
                $shiftsByActivity[$activity]['users'][] = [
                    'id' => $shift->getId(),
                    'userFullname' => $user->getFirstName().' '.$user->getLastName(),
                    'userRole' => strtolower(str_replace('ROLE_', '', $role[0]))
                ];
            }
            
            $customer = $mission->getCustomer();
            $team = $mission->getTeam();
            $location = $customer->getLocation();
            
            return new MissionShiftsDto(
                $mission->getId(),
                $mission->getStart(),
                $mission->getEnd(),
                $location->getName(),
                $team->getName(),
                $mission->getCreatedAt(),
                $mission->getUpdatedAt(),
                $shiftsByActivity
            );
        }, $missions);
    }
}