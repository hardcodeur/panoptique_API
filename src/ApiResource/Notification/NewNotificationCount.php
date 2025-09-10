<?php 
namespace App\ApiResource\Notification;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\ApiResource;
use App\Dto\Notification\NewNotificationCountDto;
use App\State\Notification\NewNotificationCountProvider;


#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/notifications/count/new',
            provider: NewNotificationCountProvider::class,
            output: NewNotificationCountDto::class,
            description: 'Count new notification after my last connection '
        )
    ]
)]
class NewNotificationCount 
{}