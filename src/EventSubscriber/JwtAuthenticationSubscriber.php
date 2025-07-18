<?php

namespace App\EventSubscriber;

use App\Entity\AuthUser;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Symfony\Component\RateLimiter\Exception\RateLimitExceededException;
use Symfony\Component\RateLimiter\RateLimiterFactory;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;


class JwtAuthenticationSubscriber implements EventSubscriberInterface
{   

    public function __construct(
        private RateLimiterFactory $loginLimiter,
        private EntityManagerInterface $em,
        private LoggerInterface $logger,
        private RequestStack $requestStack
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            'lexik_jwt_authentication.on_authentication_success' => 'onAuthenticationSuccess',
            'lexik_jwt_authentication.on_authentication_failure' => 'onAuthenticationFailure',
        ];
    }

    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event, ): void
    {   
        // Update last login field
        $data = $event->getData();
        $authUser = $event->getUser();
        
        if (!$authUser instanceof AuthUser) {
            return;
        }

        $currentDate = new \DateTimeImmutable();
        $authUser->setLastLogin($currentDate);
        $this->em->persist($authUser);
        $this->em->flush();

        $request = $this->requestStack->getCurrentRequest();

        // log
        $this->logger->info("User logged",[
            "id"=> $authUser->getId(),
            'metadata' =>[
                'ip' => $request?->getClientIp() ?? 'unknown',
                'user_agent' => $request?->headers->get('User-Agent') ?? 'unknown',
            ],
            'timestamp' => $currentDate->format('c')
        ]);

        $event->setData($data);
    }

    public function onAuthenticationFailure(AuthenticationFailureEvent $event): void
    {   
        // log
        $currentDate = new \DateTimeImmutable();
        $request = $this->requestStack->getCurrentRequest();

        $requestData = json_decode($request?->getContent() ?? '', true);
        $email = $requestData['email'] ?? 'unknown';


        $this->logger->warning("User logged fail", [
            "attempt_email" => $email,
            'metadata' => [
                'ip' => $request?->getClientIp() ?? 'unknown',
                'user_agent' => $request?->headers->get('User-Agent') ?? 'unknown',
            ],
            'timestamp' => $currentDate->format('c')
        ]);

        // Rate Limiter
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
