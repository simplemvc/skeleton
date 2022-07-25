<?php
/**
 * Skeleton application for SimpleMVC
 * 
 * @link      http://github.com/simplemvc/skeleton
 * @copyright Copyright (c) Enrico Zimuel (https://www.zimuel.it)
 * @license   https://opensource.org/licenses/MIT MIT License
 */
declare(strict_types=1);

use Psr\Container\ContainerInterface;
use SimpleMVC\Controller\Error404;
use SimpleMVC\Controller\Error405;

return [
    'routing' => [
        'routes' => require 'route.php'
    ],
    'error' => [
        '404' => Error404::class,
        '405' => Error405::class
    ],
    'bootstrap' => function(ContainerInterface $c) {
       // Put here the code to bootstrap, if any
       // e.g. a database or ORM initialization
    }
];