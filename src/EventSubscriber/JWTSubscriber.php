<?php

namespace App\EventSubscriber;

use App\Entity\AuthUser;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JWTSubscriber implements EventSubscriberInterface
{
    public function onLexikJwtAuthenticationOnJwtCreated(JWTCreatedEvent $event): void
    {
        $data = $event->getData();
        $authUserEntity = $event->getUser();
        $userEntity = $authUserEntity->getUser();

        if (!$authUserEntity instanceof AuthUser) {
            return;
        }
        $data['user_id'] = $userEntity->getId();
        $event->setData($data);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'lexik_jwt_authentication.on_jwt_created' => 'onLexikJwtAuthenticationOnJwtCreated',
        ];
    }
}
