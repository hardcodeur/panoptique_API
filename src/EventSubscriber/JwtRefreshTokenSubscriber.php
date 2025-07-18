<?php 

namespace App\EventSubscriber; 

namespace App\EventSubscriber;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

class JwtRefreshTokenSubscriber implements EventSubscriberInterface
{
    public function __construct(
        #[Autowire(service: 'limiter.refresh_token')] private RateLimiterFactory $refreshLimiter
    ) {}

    public function onKernelController(ControllerEvent $event): void
    {
        $request = $event->getRequest();
        if ($request->getPathInfo() !== '/api/token/refresh') {
            return;
        }

        // Rate limiter
        $limiter = $this->refreshLimiter->create($request->getClientIp());
        if (false === $limiter->consume()->isAccepted()) {
            throw new TooManyRequestsHttpException();
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [ControllerEvent::class => 'onKernelController'];
    }
}