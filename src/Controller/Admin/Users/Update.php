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
use App\Service\Users as ServiceUsers;
use League\Plates\Engine;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use SimpleMVC\Controller\ControllerInterface;

class Update implements ControllerInterface
{
    protected Engine $plates;
    protected ServiceUsers $users;

    public function __construct(Engine $plates, ServiceUsers $users)
    {
        $this->plates = $plates;
        $this->users = $users;
    }

    public function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $id = (int) $request->getAttribute('id');
        $params = $request->getParsedBody();

        $user = $this->users->get($id);
        $password = $params['password'] ?? '';
        $confirmPassword = $params['confirmPassword'] ?? '';
        $active = $params['active'] ?? 'off';
        
        $errors = $this->checkParams($password, $confirmPassword);
        if (!empty($errors)) {
            return new Response(
                400,
                [],
                $this->plates->render('admin::edit-user', array_merge($errors, [
                    'user' => $user
                ]))
            );
        }

        try {
            $this->users->update($id, $active === 'on' ? true : false, $password);
            return new Response(
                200,
                [],
                $this->plates->render('admin::edit-user', [
                    'result' => sprintf("The user %s has been successfully updated!", $user->username),
                    'user' => $user
                ])
            );
        } catch (DatabaseException $e) {
            // @todo log error
            return new Response(
                500,
                [],
                $this->plates->render('admin::edit-user', [
                    'error' => 'Error updating the user, please contact the administrator',
                    'user' => $user
                ])
            );
        }
    }

    /**
     * Check the parameters and returns errors if any
     * 
     * @return array<string, array<string, string>>
     */
    private function checkParams(string $password, string $confirmPassword): array
    {
        if (empty($password) && empty($confirmPassword)) {
            return [];
        }
        if (strlen($password) < User::MIN_PASSWORD_LENGHT) {
            return [
                'formErrors' => [
                    'password' => 'The password must be at least 10 characters long'
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
