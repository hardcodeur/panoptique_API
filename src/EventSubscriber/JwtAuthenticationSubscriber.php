<?php

namespace App\EventSubscriber;

use App\Entity\AuthUser;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Symfony\Component\RateLimiter\Exception\RateLimitExceededException;
use Symfony\Component\RateLimiter\RateLimiterFactory;


class JwtAuthenticationSubscriber implements EventSubscriberInterface
{   

    public function __construct(
        private RateLimiterFactory $loginLimiter,
        private EntityManagerInterface $em
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            'lexik_jwt_authentication.on_authentication_success' => 'onAuthenticationSuccess',
            'lexik_jwt_authentication.on_authentication_failure' => 'onAuthenticationFailure',
        ];
    }

    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {   
        // Update last login field
        $data = $event->getData();
        $authUser = $event->getUser();
        
        if (!$authUser instanceof AuthUser) {
            return;
        }

        $authUser->setLastLogin(new \DateTimeImmutable());
        $this->em->persist($authUser);
        $this->em->flush();

        $event->setData($data);
    }

    public function onAuthenticationFailure(AuthenticationFailureEvent $event): void
    {   
        // Rate Limiter (protection brut force)
        $request = $event->getRequest();
        $limiter = $this->loginLimiter->create($request->getClientIp());

        try {
            $limiter->consume()->ensureAccepted();
        } catch (RateLimitExceededException $e) {
            $event->getResponse()?->setContent(json_encode([
                'error' => 'Trop de tentatives de connexion. Réessayez dans 5 minutes.'
            ]));
        }
    }

}
