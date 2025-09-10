<?php 

namespace App\State\Notification;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Validator\Exception\ValidationException;
use App\Repository\UserRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

// dto
use App\Dto\Notification\NotificationCreateDto;
use App\Dto\Notification\NotificationDetailDto;
// doctrine
use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Validator\Validator\ValidatorInterface;

class NotificationProcessor implements ProcessorInterface
{   
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator,
        private UserRepository $userRepository 
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($data instanceof NotificationCreateDto) {
            return $this->handleCreate($data);
        }
        
        if ($operation instanceof Delete && $data instanceof Notification) {
            $this->handleDelete($data);
            return null;
        }

        return $data;
    }

    public function handleCreate(NotificationCreateDto $data){

        $user = $this->userRepository->find($data->getUserId());

        if (!$user) {
            throw new NotFoundHttpException(sprintf("L'utilisateur avec l'ID %d n'existe pas.", $data->getUserId()));
        }

        $item = new Notification;
        $item->setText($data->getText());
        $item->setUser($user);

        $this->entityManager->persist($item);
        $this->entityManager->flush();

        return new NotificationDetailDto(
            $item->getId(),
            $item->getText()
        );

    }


    public function handleDelete(Notification $item){
        // Delete notification
        $item->setIsDelete(true);
        $this->entityManager->flush();
    }

}