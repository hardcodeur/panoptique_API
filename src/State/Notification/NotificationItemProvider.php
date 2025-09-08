<?php

namespace App\State\Notification;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\Notification\NotificationDetailDto;
use App\Repository\NotificationRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NotificationItemProvider implements ProviderInterface
{
    public function __construct(
        private NotificationRepository $repository
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $id = $uriVariables['id'] ?? null;
        $item = $this->repository->find($id);
        
        if (!$item) {
            throw new NotFoundHttpException('Notification non trouvé');
        }
                
        return new NotificationDetailDto(
            $item->getId(),
            $item->getText()
        );
    }
    
}
