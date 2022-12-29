<?php
declare(strict_types=1);

namespace SimpleMVC\Test\Controller;

use App\Controller\Login;
use App\Service\Auth;
use App\Service\Users;
use League\Plates\Engine;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\NullLogger;

final class LoginTest extends TestCase
{
    public function setUp(): void
    {
        $this->plates = new Engine(__DIR__ . '/../../src/View');
        $this->auth = $this->createMock(Auth::class);
        $this->users = $this->createMock(Users::class);
        $this->logger = new NullLogger();

        $this->login = new Login($this->plates, $this->auth, $this->users, $this->logger);
        $this->request = $this->createMock(ServerRequestInterface::class);
        $this->response = $this->createMock(ResponseInterface::class);

        // required for the URL constants
        require_once __DIR__ . '/../../config/url_constants.php';
    }

    
    public function testGetReturn200(): void
    {
        $this->request = new ServerRequest('GET', LOGIN_URL);
        $response = $this->login->execute($this->request, $this->response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testPostWithInvalidUsersReturn400(): void
    {
        $this->request = new ServerRequest('POST', LOGIN_URL);
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
        $this->request = new ServerRequest('POST', LOGIN_URL);
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
        $this->assertEquals(DASHBOARD_URL, $response->getHeader('Location')[0]);
    }
}
