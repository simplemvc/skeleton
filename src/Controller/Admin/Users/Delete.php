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

use App\Exception\DatabaseException;
use App\Service\Users as ServiceUsers;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use SimpleMVC\Controller\ControllerInterface;

class Delete implements ControllerInterface
{
    protected ServiceUsers $users;

    public function __construct(ServiceUsers $users)
    {
        $this->users = $users;
    }

    public function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $id = (int) $request->getAttribute('id');
        // If the user is the last one I cannot delete it, otherwise no admin access anymore
        if ($this->users->getTotalUsers() < 2) {
            return new Response(
                409,
                [],
                json_encode(['error' => 'Cannot delete the user since it\'s the last one'])
            );
        }
        try {
            $this->users->delete($id);
            return new Response(
                200,
                [],
                json_encode(['result' => 'ok'])
            );
        } catch (DatabaseException $e) {
            return new Response(
                404,
                [],
                json_encode(['error' => sprintf("The user ID %d does not exist", $id)])
            );
        }
    }
}
