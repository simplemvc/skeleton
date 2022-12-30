<?php
declare(strict_types=1);

namespace SimpleMVC\Test\Controller;

use App\Config\Route;
use App\Controller\Login;
use App\Service\Auth;
use App\Service\Users;
use League\Plates\Engine;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class LoginTest extends TestCase
{
    /** @var ServerRequestInterface|MockObject */
    private $request;

    /** @var ResponseInterface|MockObject */
    private $response;

    /** @var Auth|MockObject */
    private $auth;

    /** @var Users|MockObject */
    private $users;

    private Login $login;

    private Engine $plates;

    private LoggerInterface $logger;

    public function setUp(): void
    {
        $this->plates = new Engine(__DIR__ . '/../../src/View');
        $this->auth = $this->createMock(Auth::class);
        $this->users = $this->createMock(Users::class);
        $this->logger = new NullLogger();

        $this->login = new Login($this->plates, $this->auth, $this->users, $this->logger);
        $this->request = $this->createMock(ServerRequestInterface::class);
        $this->response = $this->createMock(ResponseInterface::class);
    }

    
    public function testGetReturn200(): void
    {
        $this->request = new ServerRequest('GET', Route::LOGIN);
        $response = $this->login->execute($this->request, $this->response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testPostWithInvalidUsersReturn400(): void
    {
        $this->request = new ServerRequest('POST', Route::LOGIN);
        $this->request = $this->request->withParsedBody([
            'username' => 'admin',
            'password' => 'supersecret'
        ]);

        $this->auth->expects($this->once())
            ->method('verifyUsername')
            ->with($this->equalTo('admin'), $this->equalTo('supersecret'))
            ->willReturn(false); // invalid credentials

        $response = $this->login->execute($this->request, $this->response);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testPostWithValidUserReturn303(): void
    {
        $this->request = new ServerRequest('POST', Route::LOGIN);
        $this->request = $this->request->withParsedBody([
            'username' => 'admin',
            'password' => 'supersecret'
        ]);

        $this->auth->expects($this->once())
            ->method('verifyUsername')
            ->with($this->equalTo('admin'), $this->equalTo('supersecret'))
            ->willReturn(true); // valid credentials

        $this->users->expects($this->once())
            ->method('updateLastLogin')
            ->with($this->equalTo('admin'));

        $response = $this->login->execute($this->request, $this->response);

        $this->assertEquals(303, $response->getStatusCode());
        $this->assertEquals(Route::DASHBOARD, $response->getHeader('Location')[0]);
    }
}
