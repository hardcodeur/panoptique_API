<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

use App\Repository\UserRepository;
use App\Repository\TeamRepository;
use App\Repository\MissionRepository;
// DTO
use App\Dto\UpdateCheker\UserUpdateCheckerDto;
use App\Dto\UpdateCheker\TeamUpdateCheckerDto;
use App\Dto\UpdateCheker\MissionUpdateCheckerDto;

final class UpdateCheckerController extends AbstractController
{   

    public function __construct(
        private UserRepository $userRepository,
        private TeamRepository $teamRepository,
        private MissionRepository $missionRepository,
        private SerializerInterface $serializer
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
            $item->getTeam()->getId()
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

        $item = $this->missionRepository->find($id);

        if (!$item) {
            return new JsonResponse(['message' => 'Mission not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $dto = new MissionUpdateCheckerDto(
            $item->getId(),
            $item->getStart(),
            $item->getEnd(),
        );

        // Serialize the DTO to JSON
        $json = $this->serializer->serialize($dto, 'json');

        return new JsonResponse($json, JsonResponse::HTTP_OK, [], true);
    }
    
}
