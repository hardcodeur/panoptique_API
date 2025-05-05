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

    public static function getSubscribedEvents(): array
    {
        return [
            Events::AUTHENTICATION_SUCCESS => 'onAuthenticationSuccess',
        ];
    }

    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {   
        $data = $event->getData();
        $authUser = $event->getUser();
        
        // Vérifie si l'utilisateur est bien une instance de UserAuth
        if (!$authUser instanceof AuthUser) {
            return;
        }

        // Met à jour la date de dernière connexion
        $authUser->setLastLogin(new \DateTimeImmutable());
        $this->em->persist($authUser);
        $this->em->flush();

        // Ajoute l'ID utilisateur à la réponse
        $data['userId'] = $authUser->getUser()->getId();
        $event->setData($data);
    }
}