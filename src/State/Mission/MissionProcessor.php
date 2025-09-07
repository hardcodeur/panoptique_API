<?php 

namespace App\State\Mission;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Validator\Exception\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

// dto
use App\Dto\Mission\MissionCreateDto;
use App\Dto\Mission\MissionUpdateDto;
use App\Dto\Mission\MissionDetailDto;
use App\Dto\Mission\MissionListDto;
// doctrine
use App\Entity\Mission;
use App\Repository\TeamRepository;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\MissionRepository;

// Email
use Symfony\Component\Messenger\MessageBusInterface;
use App\Message\MessageHandler\RegisterNewMission\RegisterNewMissionData;
use App\Message\MessageHandler\RegisterDeleteMission\RegisterDeleteMissionData;

use Symfony\Component\Validator\Validator\ValidatorInterface;

class MissionProcessor implements ProcessorInterface
{   
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MissionRepository $missionRepository,
        private TeamRepository $teamRepository,
        private CustomerRepository $customerRepository,
        private ValidatorInterface $validator,
        private MessageBusInterface $messageBus,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($data instanceof MissionCreateDto) {
            return $this->handleCreate($data);
        }
        
        if ($data instanceof MissionUpdateDto) {
            $id = $uriVariables['id'] ?? null;
            return $this->handleUpdate($id,$data);
        }

        if ($operation instanceof Delete && $data instanceof Mission) {
            $this->handleDelete($data);
            return null;
        }

        return $data;
    }

    public function handleCreate(MissionCreateDto $data){

        $team = $this->teamRepository->find($data->getTeam());
        $customer = $this->customerRepository->find($data->getCustomer());

        if(!$team){
            throw new NotFoundHttpException(sprintf("L'équipe avec l'ID : %d n'existe pas", $data->getTeam()));
        }
        if(!$customer){
            throw new NotFoundHttpException(sprintf("Le client avec l'ID : %d n'existe pas", $data->getCustomer()));
        }

        $item = new Mission;
        $item->setStart($data->getStart());
        $item->setEnd($data->getEnd());
        $item->setCustomer($customer);
        $item->setTeam($team);


        # Validation of my entity 
        $violations = $this->validator->validate($item);
        if (count($violations) > 0) {
            throw new ValidationException($violations);
        }

        $this->entityManager->persist($item);
        $this->entityManager->flush();

        // Send email at all team
        $users=$team->getUsers();
        foreach ($users as $user) {
            $userEmail = $user->getAuthUser()->getEmail();
            // asynchrone send Email
            $this->messageBus->dispatch(new RegisterNewMissionData($userEmail, $item));
        }

        return new MissionDetailDto(
            $item->getId(),
            $item->getStart(),
            $item->getEnd(),
            $item->getCustomer()->getId(),
            $item->getTeam()->getId(),
            $item->getCreatedAt(),
            $item->getUpdatedAt(),
        );

    }

    public function handleUpdate(int $itemId,MissionUpdateDto $data){

        $item = $this->missionRepository->find($itemId);

        if(!$item){
            throw new NotFoundHttpException(sprintf("La mission avec l'ID : %d n'existe pas", $itemId));
        }

        # Validation of my entity 
        $violations = $this->validator->validate($item);
        if (count($violations) > 0) {
            throw new ValidationException($violations);
        }

        if($data->getStart() !== null){
            $item->setStart($data->getStart());
        }

        if($data->getEnd() !== null){
            $item->setEnd($data->getEnd());
        }

        $this->entityManager->flush();

        return new MissionDetailDto(
            $item->getId(),
            $item->getStart(),
            $item->getEnd(),
            $item->getCustomer()->getId(),
            $item->getTeam()->getId(),
            $item->getCreatedAt(),
            $item->getUpdatedAt(),
        );

    }


    public function handleDelete(Mission $item){

        // Send email at all team
        $team = $this->teamRepository->find($item->getTeam());
        $users=$team->getUsers();
        foreach ($users as $user) {
            $userEmail = $user->getAuthUser()->getEmail();
            // asynchrone send Email
            $this->messageBus->dispatch(new RegisterDeleteMissionData($userEmail, $item));
        }
        
        $this->entityManager->remove($item);
        $this->entityManager->flush();
    }

}