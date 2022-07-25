<?php
declare(strict_types=1);

namespace SimpleMVC\Test\Controller;

use App\Controller\Home;
use League\Plates\Engine;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class HomeTest extends TestCase
{
   
    /** @var ServerRequestInterface|MockObject */
    private $request;

    /** @var ResponseInterface|MockObject */
    private $response;

    private Home $home;

    private Engine $plates;

    public function setUp(): void
    {
        $this->plates = new Engine(__DIR__ . '/../../src/View');
        $this->home = new Home($this->plates);
        $this->request = $this->createMock(ServerRequestInterface::class);
        $this->response = $this->createMock(ResponseInterface::class);
    }

    public function testExecuteReturn200(): void
    {
        $response = $this->home->execute($this->request, $this->response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testExecuteHasHomeViewBody(): void
    {
        $response = $this->home->execute($this->request, $this->response);
        $this->assertEquals($this->plates->render('home'), (string) $response->getBody());
    }
}
