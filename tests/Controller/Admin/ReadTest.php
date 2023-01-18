<?php
declare(strict_types=1);

namespace SimpleMVC\Test\Controller\Admin;

use App\Config\Route;
use App\Controller\Admin\Users\Read;
use App\Exception\DatabaseException;
use App\Model\User;
use App\Service\Users;
use League\Plates\Engine;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class ReadTest extends TestCase
{
    /** @var ServerRequestInterface|MockObject */
    private $request;

    /** @var ResponseInterface|MockObject */
    private $response;

    /** @var Users|MockObject */
    private $users;

    private Engine $plates;

    private Read $read;

    public function setUp(): void
    {
        $this->users = $this->createStub(Users::class);
        $this->plates = new Engine(__DIR__ . '/../../../src/View');
        $config = require __DIR__ . '/../../../config/config.php';
        foreach ($config['view']['folders'] as $name => $folder) {
            $this->plates->addFolder($name, $folder);
        }

        $this->read = new Read($this->plates, $this->users);
        $this->request = $this->createStub(ServerRequestInterface::class);
        $this->response = $this->createMock(ResponseInterface::class);

        // Simulate a logged-in user
        $_SESSION['username'] = 'test';
    }

    public function testReadWithoutId(): void
    {
        $this->users->method('getTotalUsers')
            ->willReturn(0);
        $this->users->method('getAll')
            ->willReturn([]);

        $response = $this->read->execute($this->request, $this->response);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(
            $this->plates->render('admin::users',[
                'start' => 0,
                'size'  => Read::USERS_PER_PAGE,
                'total' => 0,
                'users' => []
            ]), 
            (string) $response->getBody()
        );
    }

    public function testReadWithIdSuccess(): void
    {
        $user = new User();
        $user->id = 1;
        $user->username = 'user';
        $user->password = 'password';
        $user->active = 0;

        $this->request->method('getAttribute')
            ->willReturn(1); // User id
        
        $this->users->method('get')
            ->willReturn($user);

        $response = $this->read->execute($this->request, $this->response);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(
            $this->plates->render('admin::edit-user',[
                'user' => $user
            ]), 
            (string) $response->getBody()
        );
    }

    public function testReadUserDoesNotExist(): void
    {
        $this->request->method('getAttribute')
            ->willReturn(1); // User id
        
        $this->users->method('get')
            ->willThrowException(new DatabaseException);

            $response = $this->read->execute($this->request, $this->response);

        $this->assertEquals(303, $response->getStatusCode());
        $this->assertEquals(Route::DASHBOARD, $response->getHeader('Location')[0]);
    }
}