<?php 

namespace App\State\Mission;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Metadata\Delete;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Entity\Notification;

// dto
use App\Dto\Mission\MissionCreateDto;
use App\Dto\Mission\MissionUpdateDto;
use App\Dto\Mission\MissionDetailDto;
// doctrine
use App\Entity\Mission;
use App\Repository\UserRepository;
use App\Repository\TeamRepository;
use App\Repository\CustomerRepository;
use App\Repository\ShiftRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\MissionRepository;
use App\Entity\Shift;

// Email
use Symfony\Component\Messenger\MessageBusInterface;
use App\Message\MessageHandler\RegisterNewMission\RegisterNewMissionData;
use App\Message\MessageHandler\RegisterNewMissionWithShift\RegisterNewMissionWithShiftData;
use App\Message\MessageHandler\RegisterDeleteMission\RegisterDeleteMissionData;
use App\Message\MessageHandler\RegisterUpdateMissionWithShift\RegisterUpdateMissionWithShiftData;

use Symfony\Component\Validator\Validator\ValidatorInterface;

class MissionProcessor implements ProcessorInterface
{   
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MissionRepository $missionRepository,
        private TeamRepository $teamRepository,
        private CustomerRepository $customerRepository,
        private ShiftRepository $shiftRepository,
        private UserRepository $userRepository,
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

        if(!empty($data->getShifts())){
            foreach ($data->getShifts() as $shiftDto) {
                foreach($shiftDto->getUsers() as $shiftUser){
                    $user = $this->userRepository->find($shiftUser);
                    if(!$user){
                        throw new NotFoundHttpException(sprintf("L'utilisateur avec l'ID : %d n'existe pas", $shiftUser));
                    }
                    $shift = new Shift();
                    $shift->setStart($shiftDto->getStart());
                    $shift->setEnd($shiftDto->getEnd());
                    $shift->setActivity($shiftDto->getActivity());
                    $shift->setUser($user);
                    
                    // Update Doctrine
                    $item->addShift($shift);
                    $this->entityManager->persist($shift);
                }
            }
        }

        $this->entityManager->persist($item);
        
        $missionShift=$item->getShifts();
        if($missionShift->isEmpty()){
            // Send email new Mission at all team
            $users=$item->getTeam()->getUsers();
            foreach ($users as $user) {
                // email
                $userEmail = $user->getAuthUser()->getEmail();
                $this->messageBus->dispatch(new RegisterNewMissionData($userEmail, $item));

                // notification
                $notificationUser = new Notification();
                $notificationUser->setText("La mission ".$item->getId()." a été assigné à votre équipe, vos quarts ne sont pas encore définis.");
                $notificationUser->setUser($user);
                $this->entityManager->persist($notificationUser);
            }
        }else{
            // Send email new Mission and Shift part user
            foreach ($missionShift as $shift) {
                // email
                $userEmail=$shift->getUser()->getAuthUser()->getEmail();
                $this->messageBus->dispatch(new RegisterNewMissionWithShiftData($userEmail, $item));

                // notification
                $notificationUser = new Notification();
                $notificationUser->setText("La mission ".$item->getId()." a été assigné à votre équipe, consulter vos horaires de travail.");
                $notificationUser->setUser($user);
                $this->entityManager->persist($notificationUser);
            }
        }

        $this->entityManager->flush();

        return new MissionDetailDto(
            $item->getId(),
            $item->getStart(),
            $item->getEnd(),
            $item->getCustomer()?->getId(),
            $item->getTeam()?->getId(),
            $item->getCreatedAt(),
            $item->getUpdatedAt(),
        );

    }

    public function handleUpdate(int $itemId, MissionUpdateDto $data)
    {
        $item = $this->missionRepository->find($itemId);

        if (!$item) {
            throw new NotFoundHttpException(sprintf("La mission avec l'ID : %d n'existe pas", $itemId));
        }

        // Part Update mission properties
        if ($data->getStart() !== null) {
            $item->setStart($data->getStart());
        }
        if ($data->getEnd() !== null) {
            $item->setEnd($data->getEnd());
        }

        // part shift
        $currentShiftsMap = [];
        foreach ($item->getShifts() as $shift) {
            if ($shift->getUser()) {
                $key = $shift->getActivity() . '|' . $shift->getUser()->getId();
                $currentShiftsMap[$key] = $shift;
            }
        }

        $desiredShiftsMap = [];
        if (!empty($data->getShifts())) {
            foreach ($data->getShifts() as $shiftDto) {
                foreach ($shiftDto->getUsers() as $userId) {
                    $key = $shiftDto->getActivity() . '|' . $userId;
                    $desiredShiftsMap[$key] = [
                        'dto' => $shiftDto,
                        'userId' => $userId
                    ];
                }
            }
        }

        // Update manager
        foreach ($desiredShiftsMap as $key => $desiredData) {
            $shiftDto = $desiredData['dto'];
            $userId = $desiredData['userId'];

            if (isset($currentShiftsMap[$key])) {
                // UPDATE 
                $shiftToUpdate = $currentShiftsMap[$key];
                $shiftToUpdate->setStart($shiftDto->getStart());
                $shiftToUpdate->setEnd($shiftDto->getEnd());

                $user = $this->userRepository->find($userId);
                if (!$user) {
                    throw new NotFoundHttpException(sprintf("L'utilisateur avec l'ID : %d n'existe pas", $userId));
                }

                $notificationUser = new Notification();
                $notificationUser->setText("La mission ".$item->getId()." a été modifier, consulter vos nouveaux horaires de travail.");
                $notificationUser->setUser($user);
                $this->entityManager->persist($notificationUser);

                unset($currentShiftsMap[$key]);
            } else {
                // CREATE
                $user = $this->userRepository->find($userId);
                if (!$user) {
                    throw new NotFoundHttpException(sprintf("L'utilisateur avec l'ID : %d n'existe pas", $userId));
                }

                $newShift = new Shift();
                $newShift->setStart($shiftDto->getStart());
                $newShift->setEnd($shiftDto->getEnd());
                $newShift->setActivity($shiftDto->getActivity());
                $newShift->setUser($user);
                $item->addShift($newShift);
                $this->entityManager->persist($newShift);

                // Notification
                $notificationUser = new Notification();
                $notificationUser->setText("Vous avez été ajouté à la mission " . $item->getId() . " (quart: " . $newShift->getActivity() . ").");
                $notificationUser->setUser($user);
                $this->entityManager->persist($notificationUser);
            }
        }

        // DELETE
        foreach ($currentShiftsMap as $key => $shiftToDelete) {
            $notificationUser = new Notification();
            $notificationUser->setText("Votre quart de " . $shiftToDelete->getActivity() . " sur la mission " . $item->getId() . " a été supprimé.");
            $notificationUser->setUser($shiftToDelete->getUser());
            $this->entityManager->persist($notificationUser);

            $item->removeShift($shiftToDelete);
            $this->entityManager->remove($shiftToDelete);
        }

        

        $this->entityManager->flush();

        return new MissionDetailDto(
            $item->getId(),
            $item->getStart(),
            $item->getEnd(),
            $item->getCustomer()?->getId(),
            $item->getTeam()?->getId(),
            $item->getCreatedAt(),
            $item->getUpdatedAt(),
        );
    }


    public function handleDelete(Mission $item){

        // Send email at all team
        $team = $this->teamRepository->find($item->getTeam());
        $users = $team->getUsers();

        foreach ($users as $user) {
            $userAuth = $user->getAuthUser();
            // asynchrone send Email
            if($userAuth){
                $userEmail = $user->getAuthUser()->getEmail();
                $this->messageBus->dispatch(new RegisterDeleteMissionData($userEmail, $item));
            }

            // notification
            $notificationUser = new Notification();
            $notificationUser->setText("La mission ".$item->getId()." a été annulé.");
            $notificationUser->setUser($user);
            $this->entityManager->persist($notificationUser);
        }
        
        $this->entityManager->remove($item);
        $this->entityManager->flush();
    }

}