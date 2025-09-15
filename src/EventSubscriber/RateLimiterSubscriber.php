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
        #[Autowire(service: 'limiter.refresh_token')] private RateLimiterFactory $refreshLimiter,
        #[Autowire(service: 'limiter.api')] private RateLimiterFactory $apiLimiter,
        #[Autowire(service: 'limiter.password_reset')] private RateLimiterFactory $passwordResetLimiter
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [RequestEvent::class => ['onKernelController', 100]];
    }

    public function onKernelController(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $path = $request->getPathInfo();
        $limiter = null;

        // Appliquer les limiteurs les plus spécifiques en premier
        switch ($path) {
            case '/api/login':
                $limiter = $this->loginLimiter->create($request->getClientIp());
                break;
            case '/api/token/refresh':
                $limiter = $this->refreshLimiter->create($request->getClientIp());
                break;
            // case '/api/reset/password':
            //     $limiter = $this->passwordResetLimiter->create($request->getClientIp());
            //     break;
        }

        // Si aucun limiteur spécifique n'a été trouvé, vérifier s'il s'agit d'une route API générale
        if ($limiter === null && str_starts_with($path, '/api/')) {
            $limiter = $this->apiLimiter->create($request->getClientIp());
        }

        // Si un limiteur a été appliqué, le consommer
        if ($limiter !== null) {
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

}