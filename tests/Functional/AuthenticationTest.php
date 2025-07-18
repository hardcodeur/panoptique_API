<?php
namespace App\Tests\Functional;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;
use App\Entity\AuthUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthenticationTest extends ApiTestCase
{
    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $passwordHasher;

    private string $testEmail = "test0001@sgs.com";
    private string $testPassword = "Root_123";

    private $client;

    protected function setUp(): void
    {   
        self::$alwaysBootKernel = false;
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->setAutoExit(false);

        // Init client
        $this->client = static::createClient();

        // Init entity manager and password hasher
        $this->em = $kernel->getContainer()->get('doctrine')->getManager();
        $this->passwordHasher = $kernel->getContainer()->get(UserPasswordHasherInterface::class);

        // Drop schema
        $inputDrop = new ArrayInput([
            'command' => 'doctrine:schema:drop',
            '--force' => true,
        ]);
        $application->run($inputDrop, new BufferedOutput());

        // Create schema
        $inputCreate = new ArrayInput([
            'command' => 'doctrine:schema:create',
        ]);
        $application->run($inputCreate, new BufferedOutput());

        // User test
        $user = new User();
        $user->setFirstName('Bouh');
        $user->setLastName('Test');
        $user->setPhone('1234567890');

        $authUser = new AuthUser();
        $authUser->setEmail($this->testEmail);
        $authUser->setPassword($this->passwordHasher->hashPassword($authUser, $this->testPassword));
        $authUser->setUser($user);

        $this->em->persist($authUser);
        $this->em->flush();

    }

    // Route /api/login valid
    public function testSuccessfulLogin(): void
    {
        $response = $this->client->request('POST', '/api/login', [
            'json' => [
                'email' => $this->testEmail,
                'password' => $this->testPassword,
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $responseData = $response->toArray();
        $this->assertArrayHasKey('token', $responseData);
        $this->assertArrayHasKey('refresh_token', $responseData);
    }

    // Route /api/login invalid
    public function testInvalidCredentials(): void
    {

        $this->client->request('POST', '/api/login', [
            'json' => [
                'email' => 'bouh@sgs.com',
                'password' => 'BOUUUUUUUUUUUUUh',
            ],
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'Invalid credentials.',
        ]);
    }

    // Route /api/token/refresh valid
    public function testSuccessfullRefreshToken(): void
    {
        // login
        $loginResponse = $this->client->request('POST', '/api/login', [
            'json' => [
                'email' => $this->testEmail,
                'password' => $this->testPassword,
            ],
        ]);
        $loginData = $loginResponse->toArray();
        $initialToken = $loginData['token'];
        $initialRefreshToken = $loginData['refresh_token'];
        sleep(1); 
        // refresh token
        $refreshResponse = $this->client->request('POST', '/api/token/refresh', [
            'json' => [
                'refresh_token' => $initialRefreshToken,
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $refreshData = $refreshResponse->toArray();
        $this->assertArrayHasKey('token', $refreshData);
        $this->assertArrayHasKey('refresh_token', $refreshData);

        // Assert that the new access token is different and the refresh token is the same
        $this->assertNotEquals($initialToken, $refreshData['token'], 'The new access token should be different.');
        $this->assertEquals($initialRefreshToken, $refreshData['refresh_token'], 'The refresh token should remain the same.');
    }

    // Route /api/token/refresh invalid
    public function testInvalidRefreshToken(): void
    {
        $badToken="3e4f9d38a120cf7d07b8c12d9f3c0b6ea9c6df13f7e626795bce4f28d194f8592bbf3bd8c9cc5a14742c5c87203e87f836f15ed5b4be4a9911647e606d8b6e68";

        $this->client->request('POST', '/api/token/refresh', [
            'json' => [
                'refresh_token' => $badToken,
            ],
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Refresh Token Not Found',
        ]);
    }


    public function testRefreshTokenIsDeletedOnLogout(): void
    {
        // Get a refresh token
        $loginResponse = $this->client->request('POST', '/api/login', [
            'json' => [
                'email' => $this->testEmail,
                'password' => $this->testPassword,
            ],
        ]);
        $loginData = $loginResponse->toArray();
        $refreshTokenValue = $loginData['refresh_token'];

        // Check refresh token exists in the db
        $refreshToken = $this->em->getRepository(\App\Entity\RefreshToken::class)->findOneBy(['refreshToken' => $refreshTokenValue]);
        $this->assertNotNull($refreshToken, 'Refresh token should exist in the database after login.');

        // Logout
        $this->client->request('POST', '/api/logout',[
            'json' => [
                'refresh_token' => $refreshTokenValue,
            ],
        ]);
        $this->assertResponseIsSuccessful();

        // Check refresh token is deleted in the DB
        $this->em->clear(); // Clear the entity manager to ensure we get a fresh result from the DB
        $refreshToken = $this->em->getRepository(\App\Entity\RefreshToken::class)->findOneBy(['refreshToken' => $refreshTokenValue]);
        $this->assertNull($refreshToken, 'Refresh token should be deleted from the database after logout.');
    }


    protected function tearDown(): void
    {
        parent::tearDown();
        $this->em->close();
    }
}
