<?php

namespace App\State\Location;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;

use App\Dto\Location\LocationListDto;
use App\Dto\LocationNote\LocationNoteDto;
use App\Repository\LocationRepository;


class LocationTeamListProvider implements ProviderInterface
{   
    public function __construct(
        private LocationRepository $locationRepository,
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
        
        $locations = $this->locationRepository->findLocationsWithNotesByUser($user->getId());
        
        return array_map(function ($location) {
            $locationNotes = [];
            foreach ($location->getLocationNotes() as $note) {
                $locationNotes[] = new LocationNoteDto(
                    $note->getId(),
                    $note->getTitle(),
                    $note->getNote(),
                    $note->getCreatedAt(),
                    $note->getUpdatedAt(),
                );
            }
            $team = $location->getTeam();
            return new LocationListDto(
                $location->getId(),
                $location->getName(),
                $location->getAddress(),
                $team->getId(),
                $team->getName(),
                $locationNotes
            );
        }, $locations);
    }
}
