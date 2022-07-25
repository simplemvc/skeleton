<?php
/**
 * Skeleton application for SimpleMVC
 * 
 * @link      http://github.com/simplemvc/skeleton
 * @copyright Copyright (c) Enrico Zimuel (https://www.zimuel.it)
 * @license   https://opensource.org/licenses/MIT MIT License
 */
declare(strict_types=1);

 use App\Controller\Secret;
use League\Plates\Engine;
use Psr\Container\ContainerInterface;

return [
    'view_path' => 'src/View',
    Engine::class => function(ContainerInterface $c) {
        return new Engine($c->get('view_path'));
    },
    Secret::class => function(ContainerInterface $c) {
        return new Secret($c->get(Engine::class), $c->get('authentication'));
    },
    'authentication' => [
        'username' => 'test',
        'password' => 'password'
    ] 
];
