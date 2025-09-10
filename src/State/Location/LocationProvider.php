<?php

namespace App\State\Location;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;

use App\Dto\Location\LocationListDto;
use App\Dto\LocationNote\LocationNoteDto;
use App\Repository\LocationRepository;

class LocationProvider implements ProviderInterface
{   
    public function __construct(
        private LocationRepository $locationRepository
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        
        $locations = $this->locationRepository->findLocationsWithNotes();
        
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
