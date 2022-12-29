<?php
declare(strict_types=1);

namespace SimpleMVC\Test\Controller;

use App\Controller\Logout;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\NullLogger;

final class LogoutTest extends TestCase
{
    public function setUp(): void
    {
        $this->logger = new NullLogger();
        $this->logout = new Logout($this->logger);

        $this->request = $this->createMock(ServerRequestInterface::class);
        $this->response = $this->createMock(ResponseInterface::class);

        // required for the URL constants
        require_once __DIR__ . '/../../config/url_constants.php';
    }

    public function testLogout(): void
    {
        $_SESSION['foo'] = 'bar';
        $response = $this->logout->execute($this->request, $this->response);

        $this->assertEmpty($_SESSION);
        $this->assertEquals(303, $response->getStatusCode());
        $this->assertEquals(LOGIN_URL, $response->getHeader('Location')[0]);
    }
}
