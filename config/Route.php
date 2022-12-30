<?php
/**
 * Skeleton application for SimpleMVC
 * 
 * @link      http://github.com/simplemvc/skeleton
 * @copyright Copyright (c) Enrico Zimuel (https://www.zimuel.it)
 * @license   https://opensource.org/licenses/MIT MIT License
 */
declare(strict_types=1);

namespace App\Config;

use App\Controller;
use App\Controller\Admin;
use SimpleMVC\Controller\BasicAuth;

class Route
{
    public const LOGIN = '/login';
    public const LOGOUT = '/logout';
    public const DASHBOARD = '/admin/users';

    public static function getRoutes(): array
    {
        return [
            [ 'GET', '/', Controller\Home::class ],
            [ 'GET', '/hello[/{name}]', Controller\Hello::class ],
            [ ['GET', 'POST'], self::LOGIN, Controller\Login::class ],
            [ 'GET', self::LOGOUT, Controller\Logout::class ],
            [ 'GET', '/basic-auth', [BasicAuth::class, Controller\Secret::class]],
            // Admin section
            [ 'GET', '/admin/users[/{id}]', [Controller\AuthSession::class, Admin\Users\Read::class]],
            [ 'POST', '/admin/users/{id}', [Controller\AuthSession::class, Admin\Users\Update::class]],
            [ 'POST', '/admin/users', [Controller\AuthSession::class, Admin\Users\Create::class]],
            [ 'DELETE', '/admin/users/{id}', [Controller\AuthSession::class, Admin\Users\Delete::class]],
        ];
    }
}