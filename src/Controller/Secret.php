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

use League\Plates\Engine;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use SimpleMVC\Controller\ControllerInterface;

class Secret implements ControllerInterface
{
    protected Engine $plates;

    /** @var string[] */
    protected array $auth;

    /**
     * @param string[] $auth
     */
    public function __construct(Engine $plates, array $auth)
    {
        $this->plates = $plates;
        $this->auth = $auth;
    }

    public function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return new Response(
            200,
            [],
            $this->plates->render('secret', [
                'username' => $this->auth['username'],
                'password' => $this->auth['password']
            ])
        );
    }
}
