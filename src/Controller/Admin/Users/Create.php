<?php
/**
 * Skeleton application for SimpleMVC
 * 
 * @link      http://github.com/simplemvc/skeleton
 * @copyright Copyright (c) Enrico Zimuel (https://www.zimuel.it)
 * @license   https://opensource.org/licenses/MIT MIT License
 */
declare(strict_types=1);

namespace App\Controller\Admin\Users;

use App\Config\Route;
use App\Exception\DatabaseException;
use App\Model\User;
use App\Service\Users;
use League\Plates\Engine;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use SimpleMVC\Controller\ControllerInterface;

class Create implements ControllerInterface
{
    protected Engine $plates;
    protected Users $users;

    public function __construct(Engine $plates, Users $users)
    {
        $this->plates = $plates;
        $this->users = $users;
    }

    public function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = $request->getParsedBody();
        // If no POST params just render new-user view
        if (empty($params)) {
            return new Response(
                200,
                [],
                $this->plates->render('admin::new-user')
            );
        }

        $username = $params['username'] ?? '';
        $password = $params['password'] ?? '';
        $confirmPassword = $params['confirmPassword'] ?? '';
        
        $errors = $this->validateParams($username, $password, $confirmPassword);
        if (!empty($errors)) {
            return new Response(
                400,
                [],
                $this->plates->render('admin::new-user', array_merge($errors, [
                    'username' => $username
                ]))
            );
        }

        try {
            $this->users->create($username, $password);
            return new Response(
                201,
                [],
                $this->plates->render('admin::new-user', [
                    'result' => sprintf("The user %s has been successfully created!", $username)
                ])
            );
        } catch (DatabaseException $e) {
            // @todo log error
            return new Response(
                500,
                [],
                $this->plates->render('admin::new-user', [
                    'error' => 'Error adding the user, please contact the administrator'
                ])
            ); 
        }
    }

    /**
     * @return array<string, array<string, string>>
     */
    private function validateParams(string $username, string $password, string $confirmPassword): array
    {
        if (empty($username)) {
            return [
                'formErrors' => [
                    'username' => 'The username cannot be empty'
                ]
            ];
        }
        if (empty($password)) {
            return [
                'formErrors' => [
                    'password' => 'The password cannot be empty'
                ]
            ];
        }
        if ($this->users->exists($username)) {
            return [
                'formErrors' => [
                    'username' => 'The username already exists!'
                ]
            ];
        }
        if (strlen($password) < User::MIN_PASSWORD_LENGHT) {
            return [
                'formErrors' => [
                    'password' => sprintf("The password must be at least %d characters long", User::MIN_PASSWORD_LENGHT)
                ]
            ];
        }
        if ($password !== $confirmPassword) {
            return [
                'formErrors' => [
                    'password' => 'The password and the confirm must be equal'
                ]
            ];
        }
        return [];
    }
}
