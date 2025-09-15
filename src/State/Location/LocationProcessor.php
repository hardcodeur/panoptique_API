<?php

namespace App\State\Location;

use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Metadata\Operation;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use App\Dto\Location\LocationCreateDto;

use App\Entity\Location;
use App\Entity\LocationNote;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Dto\Location\LocationDetailDto;
use App\Dto\LocationNote\LocationNoteDetailDto;
use App\Dto\Location\LocationUpdateDto;

use App\Repository\LocationRepository;
use App\Repository\LocationNoteRepository;

class LocationProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TeamRepository $teamRepository,
        private ValidatorInterface $validator,
        private LocationRepository $locationRepository,
        private LocationNoteRepository $locationNoteRepository
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($data instanceof LocationCreateDto) {
            return $this->handleCreate($data);
        }
        
        if ($data instanceof LocationUpdateDto) {
            $locationId = $uriVariables['id'] ?? null;
            return $this->handleUpdate($locationId,$data);
        }

        return $data;
    }


    public function handleCreate(LocationCreateDto $data){

        $team = $this->teamRepository->find($data->getTeam());
        
        if (!$team) {
            throw new NotFoundHttpException("Team not found");
        }

        $location = new Location();
        $location->setName($data->getName());
        $location->setAddress($data->getAddress());
        $location->setTeam($team);

        if(!empty($data->getLocationNote())){
            foreach ($data->getLocationNote() as $noteDto) {
                $note = new LocationNote();
                $note->setTitle($noteDto->getTitle());
                $note->setNote($noteDto->getNote());
                $note->setLocation($location);
                $this->entityManager->persist($note);
            }
        }

        $this->entityManager->persist($location);
        $this->entityManager->flush();

        $locationNote=[];
        if(!empty($location->getLocationNotes())){
            foreach($location->getLocationNotes() as $note){
                $noteLocation = new LocationNoteDetailDto(
                    $note->getId(),
                    $note->getTitle(),
                    $note->getNote(),
                    $note->getCreatedAt(),
                    $note->getUpdatedAt(),
                );
                $locationNote[]=$noteLocation;
            }
        }
        
        return new LocationDetailDto(
            $location->getId(),
            $location->getName(),
             $location->getAddress(),
            $location->getTeam()?->getId(),
            $locationNote
        );

    }

    public function handleUpdate(int $locationId,LocationUpdateDto $data){

        $location = $this->locationRepository->find($locationId);

        if(!$location){
            throw new NotFoundHttpException(sprintf("Le lieux avec l\'ID : %d n\'existe pas", $locationId));
        }

        if ($data->getName() !== null) {
            $location->setName($data->getName());
        }

        if ($data->getAddress() !== null) {
            $location->setAddress($data->getAddress());
        }

        if ($data->getTeam() !== null) {
            $team = $this->teamRepository->find(basename($data->getTeam()));
            if (!$team) {
                throw new NotFoundHttpException("Team not found");
            }
            $location->setTeam($team);
        }

        if (!empty($data->getLocationNote())) {
            foreach ($data->getLocationNote() as $noteDto) {
                $note = $this->locationNoteRepository->find($noteDto->getId());
                if ($noteDto->getTitle() !== null) {
                    $note->setTitle($noteDto->getTitle());
                }
                if ($noteDto->getNote() !== null) {
                    $note->setNote($noteDto->getNote());
                }
            }
        }

        $this->entityManager->flush();

        $locationNote=[];
        if(!empty($location->getLocationNotes())){
            foreach($location->getLocationNotes() as $note){
                $noteLocation = new LocationNoteDetailDto(
                    $note->getId(),
                    $note->getTitle(),
                    $note->getNote(),
                    $note->getCreatedAt(),
                    $note->getUpdatedAt(),
                );
                $locationNote[]=$noteLocation;
            }
        }
        
        return new LocationDetailDto(
            $location->getId(),
            $location->getName(),
            $location->getAddress(),
            $location->getTeam()?->getId(),
            $locationNote
        );
    }
}