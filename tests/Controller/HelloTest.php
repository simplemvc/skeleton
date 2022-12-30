<?php
declare(strict_types=1);

namespace SimpleMVC\Test\Controller;

use App\Controller\Hello;
use League\Plates\Engine;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class HelloTest extends TestCase
{
    /** @var ServerRequestInterface|MockObject */
    private $request;

    /** @var ResponseInterface|MockObject */
    private $response;

    private Hello $hello;

    private Engine $plates;

    public function setUp(): void
    {
        $this->plates = new Engine(__DIR__ . '/../../src/View');
        $this->hello = new Hello($this->plates);    
        $this->request = $this->createMock(ServerRequestInterface::class);
        $this->response = $this->createMock(ResponseInterface::class);
    }

    public function testExecuteReturn200(): void
    {
        $this->request->method('getAttribute')
            ->with($this->equalTo('name'), $this->equalTo('unknown'))
            ->willReturn('unknown');
        
        $response = $this->hello->execute($this->request, $this->response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testExecuteHasNameWithUrlParameter(): void
    {
        $this->request->method('getAttribute')
            ->with($this->equalTo('name'), $this->equalTo('unknown'))
            ->willReturn('alberto');

        $response = $this->hello->execute($this->request, $this->response);
        $this->assertEquals(
            $this->plates->render('hello', ['name' => 'Alberto']), 
            (string) $response->getBody()
        );
    }
}
