<?php
/**
 * Skeleton application for SimpleMVC
 * 
 * @link      http://github.com/simplemvc/skeleton
 * @copyright Copyright (c) Enrico Zimuel (https://www.zimuel.it)
 * @license   https://opensource.org/licenses/MIT MIT License
 */
declare(strict_types=1);

use App\Controller;
use SimpleMVC\Controller\BasicAuth;

require_once __DIR__ . '/url_constants.php';

return [
    [ 'GET', '/', Controller\Home::class ],
    [ 'GET', '/hello[/{name}]', Controller\Hello::class ],
    [ ['GET', 'POST'], LOGIN_URL, Controller\Login::class ],
    [ 'GET', LOGOUT_URL, Controller\Logout::class ],
    [ 'GET', '/basic-auth', [BasicAuth::class, Controller\Secret::class]],
    // Admin section
    [ 'GET', '/admin/users[/{id}]', [Controller\AuthSession::class, Controller\Admin\Users\Read::class]],
    [ 'POST', '/admin/users/{id}', [Controller\AuthSession::class, Controller\Admin\Users\Update::class]],
    [ 'POST', '/admin/users', [Controller\AuthSession::class, Controller\Admin\Users\Create::class]],
    [ 'DELETE', '/admin/users/{id}', [Controller\AuthSession::class, Controller\Admin\Users\Delete::class]],
];
