<?php
declare(strict_types=1);

namespace SimpleMVC\Test\Controller;

use App\Config\Route;
use App\Controller\Logout;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class LogoutTest extends TestCase
{
    /** @var ServerRequestInterface|MockObject */
    private $request;

    /** @var ResponseInterface|MockObject */
    private $response;

    private Logout $logout;

    private LoggerInterface $logger;

    public function setUp(): void
    {
        $this->logger = new NullLogger();
        $this->logout = new Logout($this->logger);

        $this->request = $this->createMock(ServerRequestInterface::class);
        $this->response = $this->createMock(ResponseInterface::class);
    }

    public function testLogout(): void
    {
        $_SESSION['foo'] = 'bar';
        $response = $this->logout->execute($this->request, $this->response);

        $this->assertEmpty($_SESSION);
        $this->assertEquals(303, $response->getStatusCode());
        $this->assertEquals(Route::LOGIN, $response->getHeader('Location')[0]);
    }
}
