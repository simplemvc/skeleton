<?php
/**
 * Skeleton application for SimpleMVC
 * 
 * @link      http://github.com/simplemvc/skeleton
 * @copyright Copyright (c) Enrico Zimuel (https://www.zimuel.it)
 * @license   https://opensource.org/licenses/MIT MIT License
 */
declare(strict_types=1);

use App\Config\Route;
use Monolog\Logger;
use Psr\Container\ContainerInterface;


return [
    'routing' => [
        'routes' => Route::getRoutes(),
        'cache' => 'data/cache/route.cache'
    ],
    'database' => [
        'pdo_dsn' => 'sqlite:data/db.sqlite',
    ],
    'view' => [
        'path' => 'src/View',
        'folders' => [
            'admin' => 'src/View/admin'
        ],
    ],
    'logger' => [
        'name' => 'app',
        'path' => sprintf("data/log/%s.log", date("Y_m_d")),
        'level' => Logger::DEBUG,
    ],
    // Basic authentication
    'authentication' => [
        'username' => 'test',
        'password' => '1234567890'
    ],
    'bootstrap' => function(ContainerInterface $c) {
       session_start();
    }
];