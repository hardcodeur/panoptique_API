<?php

namespace App\State\Notification;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\Notification\NewNotificationCountDto;
use App\Repository\NotificationRepository;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Bundle\SecurityBundle\Security;

class NewNotificationCountProvider implements ProviderInterface
{
    public function __construct(
        private NotificationRepository $notificationRepository,
        private Security $security
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): NewNotificationCountDto
    {
        /** @var \App\Entity\AuthUser|null $authUser */
        $authUser = $this->security->getUser();

        if (!$authUser) {
            throw new UnauthorizedHttpException('Bearer', 'User not authenticated.');
        }
        
        $lastLogin = $authUser->getLastLogin();

        $user = $authUser->getUser();


        if ($lastLogin === null) {
            return new NewNotificationCountDto(0);
        }

        $nbNotification = $this->notificationRepository->countLatestActiveNotificationsBeforLastConnectionByUser($user, $lastLogin);
        
        // $nbNotification can legitimately be 0, so no exception needed here.
        return new NewNotificationCountDto($nbNotification);
    }
}