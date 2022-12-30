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
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use SimpleMVC\Controller\ControllerInterface;

class Logout implements ControllerInterface
{
    protected LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (isset($_SESSION["username"])) {
            $this->logger->info(sprintf("Logout user %s", $_SESSION["username"]));
        }
        if (session_status() == PHP_SESSION_ACTIVE) {
            session_destroy();
            session_unset();
            session_regenerate_id(true);
        }
        $_SESSION = [];
        return new Response(
            303,
            ['Location' => Route::LOGIN]
        );
    }
}