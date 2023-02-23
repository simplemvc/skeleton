## Skeleton application for SimpleMVC

[![Build status](https://github.com/simplemvc/skeleton/workflows/PHP%20test/badge.svg)](https://github.com/simplemvc/skeleton/actions)

This is a skeleton web application for [SimpleMVC](https://github.com/simplemvc/framework) framework.
## Quickstart

You can install the skeleton application using the following command:

```
composer create-project simplemvc/skeleton
```

This will create a `skeleton` folder containing a basic web application.
You can execute the application using the PHP internal web server, as follows:

```
composer run-script start
```

The application will be executed at [http://localhost:8080](http://localhost:8080).

This skeleton uses [PHP-DI](https://php-di.org/) as DI container and [Plates](https://platesphp.com/)
as template engine.

## Configuration

The application is configured using the ([config/config.php](config/config.php)) file:

```php
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
```
Each section contains the configuration of the routing system (`routing`), 
database (`database`), etc.

## Routing system

The routing system uses a Route class as follows ([config/Route.php](config/Route.php)):

```php
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
```
THis class contains onlya static method that returns the list of routes as array.
A route is an element of the array with an HTTP method, a URL and a controller class to be executed. 
The URL can be specified using the FastRoute [syntax](https://github.com/nikic/FastRoute/blob/master/README.md).
The controller class can be specified also with a pipeline of controllers, as array.

For instance, the `GET /admin/users[/{id}]` route has a pipeline with `[Controller\AuthSession, Admin\Users\Read]`.
The controllers in the pipeline are executed in order, that means first `Controller\AuthSession` and last `Admin\Users\Read`.

## Routing cache

The configuration of the skeleton application enables a caching folder for the routes.

```php
return [
    'routing' => [
        'routes' => Route::getRoutes(),
        'cache' => 'data/cache/route.cache'
    ],
    // ...
]
```

Every time you change a route, you need to clean the cache using the following command:

```
composer run-script clean
```

If you want to disable the cache you can just comment (or delete) the key from
the configuration array, as follows:

```php
return [
    'routing' => [
        'routes' => Route::getRoutes(),
        // 'cache' => 'data/cache/route.cache'
    ],
    // ...
]
```

## Controller

Each controller in SimpleMvc implements the `ControllerInterface`, as follows:

```php
namespace SimpleMVC\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface ControllerInterface
{
    public function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface;
}
```

The `execute()` function accepts two parameters, the `$request` and the optional `$response`.
These are [PSR-7](https://www.php-fig.org/psr/psr-7/) HTTP request and response objects.
The response is typically used when you need to execute a pipeline of multiple controllers, where
you may want to pass the response from one controller to another.

The return of the `execute()` function is a PSR-7 Response. 
For instance, the `Home` controller reported in the skeleton application is as follows:

```php
namespace SimpleMVC\Controller;

use League\Plates\Engine;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use SimpleMVC\Controller\ControllerInterface;

class Home implements ControllerInterface
{
    protected Engine $plates;

    public function __construct(Engine $plates)
    {
        $this->plates = $plates;
    }

    public function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return new Response(
            200,
            [],
            $this->plates->render('home')
        );
    }
}
```

The `execute()` function returns a PSR-7 `Nyholm\Psr7\Response` object using the [nyholm/psr7](https://github.com/Nyholm/psr7)
project.


## Execute a controller pipeline

In the route configuration file you can specify an array of controller to be executed.
For instance, you can speficy that the `Secret` controller needs HTTP authentication
and you can implement the logic in a separate `Auth` controller.
The `config/route.php` configuration contains an example:

```php
use SimpleMVC\Controller;

return [
    // ...
    [ 'GET', '/basic-auth', [ BasicAuth::class, Controller\Secret::class ]]
];
```

The third element of the array is an array itself, containing the list of controller to be executed.
The order of execution is the same of the array, that means `BasicAuth` will be executed first
and `Secret` after.

If you want, you can halt the execution flow of SimpleMVC returning a [HaltResponse](https://github.com/simplemvc/framework/blob/main/src/Response/HaltResponse.php) object.
This response is just an empty class that extends a PSR-7 request to inform SimpleMVC to **stop the execution**.

SimpleMVC provideds a [Basic Access Authentication](https://en.wikipedia.org/wiki/Basic_access_authentication)
using the [BasicAuth](https://github.com/simplemvc/framework/blob/main/src/Controller/BasicAuth.php) controller.


## Dependecy injection container

All the dependencies are managed using the [PHP-DI](https://php-di.org/) project.

The dependency injection container is configured in [config/container.php](config/container.php) file.

## Front controller

A SimpleMVC application is executed using the `public/index.php` file.
All the HTTP requests pass from this file, that is called **Front controller**.

The front controller is as follows:

```php
use DI\ContainerBuilder;
use SimpleMVC\App;
use SimpleMVC\Emitter\SapiEmitter;

$builder = new ContainerBuilder();
$builder->addDefinitions('config/container.php');

$app = new App($builder->build());
$app->bootstrap();
$request = App::buildRequestFromGlobals();
$response = $app->dispatch($request);
SapiEmitter::emit($response);
```

In this file we build the DI container, reading from the [config/container.php](config/container.php)
file and we inject it to the [SimpleMVC\App]() class.

The application configuration is stored in the DI container with the `config` key.

We execute the `bootstrap` function. This is a special function used to initialize the application status,
e.g [starting the PHP session](https://github.com/simplemvc/skeleton/blob/main/config/config.php#L40-L42). 

After, we build the PSR-7 HTTP request using `App::buildRequestFromGlobals()` from
the PHP global variables $_GET, $_POST, $_SERVER, etc.

Then, we execute the `dispatch` function that executes the Controller(s) according to the route.

Finally, we render the PSR-7 response to the standard output using the `SapiEmitter`.

### Copyright

The author of this software is [Enrico Zimuel](https://github.com/ezimuel/) and other [contributors](https://github.com/simplemvc/skeleton/graphs/contributors).

This software is released under the [MIT](/LICENSE) license.
