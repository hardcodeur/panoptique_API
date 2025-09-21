<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

use App\Repository\UserRepository;
use App\Repository\TeamRepository;
use App\Repository\MissionRepository;
use App\Repository\LocationRepository;
// DTO
use App\Dto\UpdateCheker\UserUpdateCheckerDto;
use App\Dto\UpdateCheker\TeamUpdateCheckerDto;
use App\Dto\UpdateCheker\MissionUpdateCheckerDto;
use App\Dto\UpdateCheker\locationUpdateCheckerDto;
use App\Dto\UpdateCheker\locationNoteUpdateCheckerDto;

final class UpdateCheckerController extends AbstractController
{   

    public function __construct(
        private UserRepository $userRepository,
        private TeamRepository $teamRepository,
        private MissionRepository $missionRepository,
        private LocationRepository $locationRepository,
        private SerializerInterface $serializer,
    ) {
    }

    #[Route('api/update/checker/user/{id}', name: 'api_update_checker_user',methods:["GET"])]
    public function updateCheckerUser(int $id): JsonResponse
    {
        $item = $this->userRepository->find($id);

        if (!$item) {
            return new JsonResponse(['message' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $dto = new UserUpdateCheckerDto(
            $item->getId(),
            $item->getFirstName(),
            $item->getLastName(),
            $item->getAuthUser()->getEmail(),
            $item->getPhone(),
            $item->getStatus(),
            $item->getAuthUser()->getRoles()[0],
            $item->getTeam()?->getId()
        );

        // Serialize the DTO to JSON
        $json = $this->serializer->serialize($dto, 'json');

        return new JsonResponse($json, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('api/update/checker/team/{id}', name: 'api_update_checker_team',methods:["GET"])]
    public function updateCheckerTeam(int $id): JsonResponse
    {
        $item = $this->teamRepository->find($id);

        if (!$item) {
            return new JsonResponse(['message' => 'Team not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $dto = new TeamUpdateCheckerDto(
            $item->getId(),
            $item->getName(),
        );

        // Serialize the DTO to JSON
        $json = $this->serializer->serialize($dto, 'json');

        return new JsonResponse($json, JsonResponse::HTTP_OK, [], true);
    }


    #[Route('api/update/checker/mission/{id}', name: 'api_update_checker_mission',methods:["GET"])]
    public function updateCheckerMission(int $id): JsonResponse
    {

        $item = $this->missionRepository->findMissionWithShiftById($id);

        if (!$item) {
            return new JsonResponse(['message' => 'Mission not found'], JsonResponse::HTTP_NOT_FOUND);
        }

            $shiftsByActivityAndTime = [
                'connexion' => [],
                'surveillance' => [],
                'deconnexion' => []
            ];
            
            // regrouper les shifts par activité et créneau horaire
            foreach ($item->getShifts() as $shift) {
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
                        'users' => []
                    ];
                }
                
                $shiftsByActivityAndTime[$activity][$timeKey]['users'][] = $user->getId();
            }
            
            // Deuxième passage : réorganiser dans la structure finale
            $shiftsDto = [];
            
            foreach ($shiftsByActivityAndTime as $activity => $timeSlots) {
                foreach ($timeSlots as $timeSlot) {
                    $shiftsDto[] = $timeSlot;
                }
            }
            
            $dto = new MissionUpdateCheckerDto(
                $item->getId(),
                $item->getStart(),
                $item->getEnd(),
                $shiftsDto
            );

        // Serialize the DTO to JSON
        $json = $this->serializer->serialize($dto, 'json');

        return new JsonResponse($json, JsonResponse::HTTP_OK, [], true);

    }

    #[Route('api/update/checker/location/{id}', name: 'api_update_checker_location',methods:["GET"])]
    public function updateCheckerLocation(int $id): JsonResponse
    {

        $item = $this->locationRepository->find($id);

        if (!$item) {
            return new JsonResponse(['message' => 'Location not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $locationNotes=[];
        if(!empty($item->getLocationNotes())){
            foreach($item->getLocationNotes() as $note){
                $locationNote = new locationNoteUpdateCheckerDto(
                    $note->getId(),
                    $note->getTitle(),
                    $note->getNote(),
                );
                $locationNotes[]=$locationNote;
            }
        }

        $dto = new locationUpdateCheckerDto(
            $item->getid(),
            $item->getName(),
            $item->getAddress(),
            $item->getTeam()?->getId(),
            $locationNotes
        );

        // Serialize the DTO to JSON
        $json = $this->serializer->serialize($dto, 'json');

        return new JsonResponse($json, JsonResponse::HTTP_OK, [], true);
    }
    
}
