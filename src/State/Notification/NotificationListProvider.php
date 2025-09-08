<?php

namespace App\State\Notification;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use App\Dto\Notification\NotificationListDto;
use App\Repository\NotificationRepository;
use App\Repository\UserRepository;

class NotificationListProvider implements ProviderInterface
{   
    public function __construct(
        private NotificationRepository $notificationRepository,
        private UserRepository $userRepository
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {   
        $id = $uriVariables["id"];
        $user = $this->userRepository->find($id);
        
        if(!$user){
            throw new NotFoundHttpException(sprintf("L'utilisateur avec l'ID %d n'existe pas.", $id));
        }

        $items = $this->notificationRepository->findLatestActiveNotificationsByUser($user);
        return array_map(function ($item) {                                        
            return new NotificationListDto(
                $item->getId(),
                $item->getText(),
            );
        }, $items);
    }
}