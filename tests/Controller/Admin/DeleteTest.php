<?php
declare(strict_types=1);

namespace SimpleMVC\Test\Controller\Admin;

use App\Controller\Admin\Users\Delete;
use App\Exception\DatabaseException;
use App\Service\Users;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class DeleteTest extends TestCase
{
    /** @var ServerRequestInterface|MockObject */
    private $request;

    /** @var ResponseInterface|MockObject */
    private $response;

    /** @var Users|MockObject */
    private $users;

    private Delete $delete;

    public function setUp(): void
    {
        $this->users = $this->createStub(Users::class);

        $this->delete = new Delete($this->users);
        $this->request = $this->createStub(ServerRequestInterface::class);
        $this->response = $this->createMock(ResponseInterface::class);

        // Simulate a logged-in user
        $_SESSION['username'] = 'test';
    }

    public function testDeleteTheLastUser(): void
    {
        $this->request->method('getAttribute')
            ->willReturn(1); // User id

        $this->users->method('getTotalUsers')
            ->willReturn(1);

        $response = $this->delete->execute($this->request, $this->response);

        $this->assertEquals(409, $response->getStatusCode());
        $this->assertEquals(
            json_encode(['error' => 'Cannot delete the user since it\'s the last one']), 
            (string) $response->getBody()
        );
    }

    public function testDeleteSuccess(): void
    {
        $this->request->method('getAttribute')
            ->willReturn(1); // User id

        $this->users->method('getTotalUsers')
            ->willReturn(2);

        $response = $this->delete->execute($this->request, $this->response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(
            json_encode(['result' => 'ok']), 
            (string) $response->getBody()
        );
    }

    public function testDeleteDatabaseException(): void
    {
        $this->request->method('getAttribute')
            ->willReturn(100); // User id

        $this->users->method('getTotalUsers')
            ->willReturn(2);

        $this->users->method('delete')
            ->willThrowException(new DatabaseException);

        $response = $this->delete->execute($this->request, $this->response);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals(
            json_encode(['error' => 'The user ID 100 does not exist']), 
            (string) $response->getBody()
        );
    }
}