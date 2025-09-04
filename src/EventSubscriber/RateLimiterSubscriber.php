<?php 

namespace App\EventSubscriber; 

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\RateLimiter\Exception\RateLimitExceededException;
use Symfony\Component\HttpFoundation\JsonResponse;

class RateLimiterSubscriber implements EventSubscriberInterface
{   

    public function __construct(
        #[Autowire(service: 'limiter.login')] private RateLimiterFactory $loginLimiter,
        #[Autowire(service: 'limiter.refresh_token')] private RateLimiterFactory $refreshLimiter
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [RequestEvent::class => ['onKernelController', 100]];
    }

    public function onKernelController(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $path = $request->getPathInfo();

        $limiterMapping = [
            // '/api/login' => $this->loginLimiter,
            // '/api/token/refresh' => $this->refreshLimiter,
        ];

        if (!isset($limiterMapping[$path])) {
            return;
        }

        $limiter = $limiterMapping[$path]->create($request->getClientIp());

        try {
            $limiter->consume()->ensureAccepted();
        } catch (RateLimitExceededException) {
            $event->setResponse(
                new JsonResponse(
                    ['message' => 'Too many requests'],
                    Response::HTTP_TOO_MANY_REQUESTS
                )
            );
        }
    }

}