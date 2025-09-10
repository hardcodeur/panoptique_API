<?php

namespace App\Dto\Notification;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\State\Notification\NewNotificationCountProvider;

#[ApiResource(
    uriTemplate: '/notifications/new/count',
    operations: [
        new Get(
            provider: NewNotificationCountProvider::class,
            output: self::class,
            name: 'get_new_notification_count'
        )
    ],
    read: true,
    paginationEnabled: false,
)]
class NewNotificationCountDto
{
    public int $count = 0;

    public function __construct(int $count)
    {
        $this->count = $count;
    }
}
