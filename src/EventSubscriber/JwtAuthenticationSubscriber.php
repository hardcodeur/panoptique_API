<?php

namespace App\EventSubscriber;

use App\Entity\AuthUser;
use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
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
            'lexik_jwt_authentication.on_jwt_created' => 'onJwtCreated',
            'lexik_jwt_authentication.on_authentication_success' => 'onAuthenticationSuccess',
            'lexik_jwt_authentication.on_authentication_failure' => 'onAuthenticationFailure',
        ];
    }

    private const ROLE = [
        "ROLE_ADMIN"=>"admin",
        "ROLE_MANAGER"=>"manager",
        "ROLE_TEAM_MANAGER"=>"team_manager",
        "ROLE_USER"=>"agent"
    ];

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
    }

    public function onJwtCreated(JWTCreatedEvent $event): void
    {
        $payload = $event->getData();
        $authUser = $event->getUser();

        if (!$authUser instanceof AuthUser) {
            return;
        }

        $userProfile = $authUser->getUser();

        if (!$userProfile instanceof User) {
            return;
        }

        $payload['id'] = $userProfile->getId();
        $payload['userName'] = $userProfile->getFirstName()." ".$userProfile->getLastName();
        $payload['role'] = self::ROLE[$payload['roles'][0]];
        unset($payload['roles'],);

        $event->setData($payload);
    }
}
