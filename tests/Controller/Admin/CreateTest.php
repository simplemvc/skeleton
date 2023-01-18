<?php
declare(strict_types=1);

namespace SimpleMVC\Test\Controller\Admin;

use App\Controller\Admin\Users\Create;
use App\Model\User;
use App\Service\Users;
use League\Plates\Engine;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class CreateTest extends TestCase
{
    /** @var ServerRequestInterface|MockObject */
    private $request;

    /** @var ResponseInterface|MockObject */
    private $response;

    /** @var Users|MockObject */
    private $users;

    private Create $create;

    private Engine $plates;

    public function setUp(): void
    {
        $this->plates = new Engine(__DIR__ . '/../../../src/View');
        $config = require __DIR__ . '/../../../config/config.php';
        foreach ($config['view']['folders'] as $name => $folder) {
            $this->plates->addFolder($name, $folder);
        }

        $this->users = $this->createStub(Users::class);

        $this->create = new Create($this->plates, $this->users);
        $this->request = $this->createStub(ServerRequestInterface::class);
        $this->response = $this->createMock(ResponseInterface::class);

        // Simulate a logged-in user
        $_SESSION['username'] = 'test';
    }

    public function testCreateWithNoPostData(): void
    {
        $this->request->method('getParsedBody')
            ->willReturn([]);

        $response = $this->create->execute($this->request, $this->response);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(
            $this->plates->render('admin::new-user'), 
            (string) $response->getBody()
        );
    }

    public function testCreateWithEmptyUsername(): void
    {
        $this->request->method('getParsedBody')
            ->willReturn([
                'username' => ''
            ]);

        $response = $this->create->execute($this->request, $this->response);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals(
            $this->plates->render('admin::new-user', [
                'username' => '',
                'formErrors' => [
                    'username' => 'The username cannot be empty'
                ]
            ]), 
            (string) $response->getBody()
        );    
    }

    public function testCreateWithEmptyPassword(): void
    {
        $this->request->method('getParsedBody')
            ->willReturn([
                'username' => 'test',
                'password' => ''
            ]);

        $response = $this->create->execute($this->request, $this->response);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals(
            $this->plates->render('admin::new-user', [
                'username' => 'test',
                'formErrors' => [
                    'password' => 'The password cannot be empty'
                ]
            ]), 
            (string) $response->getBody()
        );    
    }

    public function testCreateWithExistingUsername(): void
    {
        $this->request->method('getParsedBody')
            ->willReturn([
                'username' => 'test',
                'password' => 'password'
            ]);

        $this->users->method('exists')
            ->willReturn(true);

        $response = $this->create->execute($this->request, $this->response);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals(
            $this->plates->render('admin::new-user', [
                'username' => 'test',
                'formErrors' => [
                    'username' => 'The username already exists!'
                ]
            ]), 
            (string) $response->getBody()
        );    
    }

    public function testCreateWithPasswordLengthLessMin(): void
    {
        $this->request->method('getParsedBody')
            ->willReturn([
                'username' => 'test',
                'password' => '1234'
            ]);

        $this->users->method('exists')
            ->willReturn(false);

        $response = $this->create->execute($this->request, $this->response);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals(
            $this->plates->render('admin::new-user', [
                'username' => 'test',
                'formErrors' => [
                    'password' => sprintf("The password must be at least %d characters long", User::MIN_PASSWORD_LENGHT)
                ]
            ]), 
            (string) $response->getBody()
        );    
    }

    public function testCreateWithPasswordAndConfirmationNotEqual(): void
    {
        $this->request->method('getParsedBody')
            ->willReturn([
                'username' => 'test',
                'password' => '1234567890',
                'confirmPassword' => 'password'
            ]);

        $this->users->method('exists')
            ->willReturn(false);

        $response = $this->create->execute($this->request, $this->response);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals(
            $this->plates->render('admin::new-user', [
                'username' => 'test',
                'formErrors' => [
                    'password' => 'The password and the confirm must be equal'
                ]
            ]), 
            (string) $response->getBody()
        );    
    }

    public function testCreateWithSuccess(): void
    {
        $username = 'test';
        $this->request->method('getParsedBody')
            ->willReturn([
                'username' => $username,
                'password' => '1234567890',
                'confirmPassword' => '1234567890'
            ]);

        $this->users->method('exists')
            ->willReturn(false);

        $response = $this->create->execute($this->request, $this->response);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals(
            $this->plates->render('admin::new-user', [
                'result' => sprintf("The user %s has been successfully created!", $username)
            ]), 
            (string) $response->getBody()
        );   
    }
}