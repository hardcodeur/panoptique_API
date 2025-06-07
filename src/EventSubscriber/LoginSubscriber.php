<?php

namespace App\EventSubscriber;

use App\Entity\AuthUser;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\EntityManagerInterface;

class LoginSubscriber implements EventSubscriberInterface
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {   
        $data = $event->getData();
        $authUser = $event->getUser();
        
        if (!$authUser instanceof AuthUser) {
            return;
        }

        // Mise à jour
        $authUser->setLastLogin(new \DateTimeImmutable());
        $this->em->persist($authUser);
        $this->em->flush();

        $event->setData($data);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Events::AUTHENTICATION_SUCCESS => 'onAuthenticationSuccess',
        ];
    }
}