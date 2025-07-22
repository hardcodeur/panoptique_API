<?php

namespace App\Tests\Functional;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class RateLimiterTest extends ApiTestCase
{
    private $client;

    protected function setUp(): void
    {   
        self::$alwaysBootKernel = false;
        $this->client = static::createClient();
    }

    // login route
    public function testLoginRateLimiter(): void
    {
        // 6 attempts should be allowed
        for ($i = 0; $i < 6; $i++) {
            $this->client->request('POST', '/api/login', [
                'json' => [
                    'email' => 'test@example.com',
                    'password' => 'wrong-password',
                ],
            ]);
        }

        // 7th attempt should be blocked
        $this->client->request('POST', '/api/login', [
            'json' => [
                'email' => 'test@example.com',
                'password' => 'wrong-password',
            ],
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_TOO_MANY_REQUESTS);
    }

    // refresh token route
    public function testRefreshTokenRateLimiter(): void
    {
        // 20 attempts should be allowed
        for ($i = 0; $i < 20; $i++) {
            $this->client->request('POST', '/api/token/refresh', [
                'json' => [
                    'refresh_token' => 'invalid-token',
                ],
            ]);
        }

        // 21st attempt should be blocked
        $this->client->request('POST', '/api/token/refresh', [
            'json' => [
                'refresh_token' => 'invalid-token',
            ],
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_TOO_MANY_REQUESTS);
    }
}
