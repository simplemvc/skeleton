<?php
/**
 * Skeleton application for SimpleMVC
 * 
 * @link      http://github.com/simplemvc/skeleton
 * @copyright Copyright (c) Enrico Zimuel (https://www.zimuel.it)
 * @license   https://opensource.org/licenses/MIT MIT License
 */
declare(strict_types=1);

namespace App\Controller;

use App\Config\Route;
use App\Exception\DatabaseException;
use App\Service\Auth;
use App\Service\Users;
use League\Plates\Engine;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use SimpleMVC\Controller\ControllerInterface;
use SimpleMVC\Controller\RouteTrait;

class Login implements ControllerInterface
{
    use RouteTrait;

    protected Engine $plates;
    protected Auth $auth;
    protected Users $users;
    protected LoggerInterface $logger;

    public function __construct(Engine $plates, Auth $auth, Users $users, LoggerInterface $logger)
    {
        $this->plates = $plates;
        $this->auth = $auth;
        $this->users = $users;
        $this->logger = $logger;
    }

    protected function get(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return new Response(
            200, 
            [], 
            $this->plates->render('login', ['login_url' => Route::LOGIN])
        );
    }

    protected function post(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $params = $request->getParsedBody();

        $username = $params['username'] ?? null;
        $password = $params['password'] ?? null;

        if (empty($username) || empty($password) || false === $this->auth->verifyUsername($username, $password)) {
            if (!empty($username) && !empty($password)) {
                $this->logger->warning(sprintf("Invalid credentials for user %s", $username));
            }
            return new Response(
                400, 
                [], 
                $this->plates->render('login', [
                    'error' => 'Invalid credentials',
                    'login_url' => Route::LOGIN
                ])
            );
        }
        $_SESSION['username'] = $username;
        try {
            $this->users->updateLastLogin($username);
        } catch (DatabaseException $e) {
            $this->logger->error(sprintf("Update last login: %s", $e->getMessage()));
        }
        $this->logger->info(sprintf("Login user %s", $username));
        // Redirect to ADMIN_URL
        return new Response(
            303,
            ['Location' => Route::DASHBOARD]
        );
    }
}
