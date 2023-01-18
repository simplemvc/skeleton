<?php
declare(strict_types=1);

namespace SimpleMVC\Test\Controller;

use App\Config\Route;
use App\Controller\AuthSession;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use SimpleMVC\Response\HaltResponse;

final class AuthSessionTest extends TestCase
{
    /** @var ServerRequestInterface|MockObject */
    private $request;

    /** @var ResponseInterface|MockObject */
    private $response;

    private AuthSession $authSession;

    public function setUp(): void
    {
        $this->authSession = new AuthSession();
        $this->request = $this->createMock(ServerRequestInterface::class);
        $this->response = $this->createMock(ResponseInterface::class);
    }

    public function testAuthSessionPassThrough(): void
    {
        $_SESSION['username'] = 'test';
        $response = $this->authSession->execute($this->request, $this->response);
        $this->assertEquals($this->response, $response);
    }

    public function testAuthSessionRedirectToLogin(): void
    {
        unset($_SESSION['username']);
        $response = $this->authSession->execute($this->request, $this->response);
        $this->assertInstanceOf(HaltResponse::class, $response);
        $this->assertEquals(303, $response->getStatusCode());
        $this->assertEquals(Route::LOGIN, $response->getHeader('Location')[0]);
    }
}
