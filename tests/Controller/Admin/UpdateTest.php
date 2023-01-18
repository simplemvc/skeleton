<?php
declare(strict_types=1);

namespace SimpleMVC\Test\Controller\Admin;

use App\Config\Route;
use App\Controller\Admin\Users\Update;
use App\Exception\DatabaseException;
use App\Model\User;
use App\Service\Users;
use League\Plates\Engine;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class UpdateTest extends TestCase
{
    /** @var ServerRequestInterface|MockObject */
    private $request;

    /** @var ResponseInterface|MockObject */
    private $response;

    /** @var Users|MockObject */
    private $users;

    private Engine $plates;

    private Update $update;

    public function setUp(): void
    {
        $this->users = $this->createStub(Users::class);
        $this->plates = new Engine(__DIR__ . '/../../../src/View');
        $config = require __DIR__ . '/../../../config/config.php';
        foreach ($config['view']['folders'] as $name => $folder) {
            $this->plates->addFolder($name, $folder);
        }

        $this->update = new Update($this->plates, $this->users);
        $this->request = $this->createStub(ServerRequestInterface::class);
        $this->response = $this->createMock(ResponseInterface::class);

        // Simulate a logged-in user
        $_SESSION['username'] = 'test';
    }

    public function testUpdateWithShortPassword(): void
    {
        $user = new User();
        $user->id = 1;
        $user->username = 'user';
        $user->active = 0;

        $this->request->method('getAttribute')
            ->willReturn(1); // User id
        
        $this->request->method('getParsedBody')
            ->willReturn([
                'password' => '1234',
                'confirmPassword' => '1234',
                'active' => false
            ]);

        $this->users->method('get')
            ->willReturn($user);

        $response = $this->update->execute($this->request, $this->response);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals(
            $this->plates->render('admin::edit-user', [
                'user' => $user,
                'formErrors' => [
                    'password' => 'The password must be at least 10 characters long'
                ]
            ]), 
            (string) $response->getBody()
        );
    }

    public function testUpdateWithDifferentPasswordAndConfirm(): void
    {
        $user = new User();
        $user->id = 1;
        $user->username = 'user';
        $user->active = 0;

        $this->request->method('getAttribute')
            ->willReturn(1); // User id
        
        $this->request->method('getParsedBody')
            ->willReturn([
                'password' => '1234567890',
                'confirmPassword' => '12345678900',
                'active' => false
            ]);

        $this->users->method('get')
            ->willReturn($user);

        $response = $this->update->execute($this->request, $this->response);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals(
            $this->plates->render('admin::edit-user', [
                'user' => $user,
                'formErrors' => [
                    'password' => 'The password and the confirm must be equal'
                ]
            ]), 
            (string) $response->getBody()
        );
    }

    public function testUpdateSuccess(): void
    {
        $user = new User();
        $user->id = 1;
        $user->username = 'user';
        $user->active = 0;

        $this->request->method('getAttribute')
            ->willReturn(1); // User id
        
        $this->request->method('getParsedBody')
            ->willReturn([
                'password' => '1234567890',
                'confirmPassword' => '1234567890',
                'active' => false
            ]);

        $this->users->method('get')
            ->willReturn($user);

        $response = $this->update->execute($this->request, $this->response);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(
            $this->plates->render('admin::edit-user', [
                'result' => sprintf("The user %s has been successfully updated!", $user->username),
                'user' => $user
            ]), 
            (string) $response->getBody()
        );
    }

    public function testUpdateDatabaseException() : void
    {
        $user = new User();
        $user->id = 1;
        $user->username = 'user';
        $user->active = 0;

        $this->request->method('getAttribute')
            ->willReturn(1); // User id
        
        $this->request->method('getParsedBody')
            ->willReturn([
                'password' => '1234567890',
                'confirmPassword' => '1234567890',
                'active' => false
            ]);

        $this->users->method('get')
            ->willReturn($user);

        $this->users->method('update')
            ->willThrowException(new DatabaseException);

        $response = $this->update->execute($this->request, $this->response);

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals(
            $this->plates->render('admin::edit-user', [
                'error' => 'Error updating the user, please contact the administrator',
                'user' => $user
            ]), 
            (string) $response->getBody()
        );    
    }
}