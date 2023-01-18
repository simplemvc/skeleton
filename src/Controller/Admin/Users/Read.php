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

use App\Config\Route;
use App\Exception\DatabaseException;
use App\Service\Users as ServiceUsers;
use League\Plates\Engine;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use SimpleMVC\Controller\ControllerInterface;
use SimpleMVC\Response\HaltResponse;

class Read implements ControllerInterface
{
    const USERS_PER_PAGE = 10;

    protected Engine $plates;
    protected ServiceUsers $users;

    public function __construct(Engine $plates, ServiceUsers $users)
    {
        $this->plates = $plates;
        $this->users = $users;
    }

    public function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $id = $request->getAttribute('id', null);
        if (empty($id)) {
            $params = $request->getQueryParams();
            $start = (int) ($params['start'] ?? 0);
            $size = (int) ($params['size'] ?? self::USERS_PER_PAGE);
            return new Response(
                200,
                [],
                $this->plates->render('admin::users', [
                    'start' => $start,
                    'size' => $size,
                    'total' => $this->users->getTotalUsers(),
                    'users' => $this->users->getAll($start, $size)
                ])
            );
        }
        try {
            $user = $this->users->get((int) $id);
            return new Response(
                200,
                [],
                $this->plates->render('admin::edit-user', [
                    'user' => $user
                ])
            );
        } catch (DatabaseException $e) {
            // @todo log the user does not exist
            return new HaltResponse(
                303,
                ['Location' => Route::DASHBOARD]
            );  
        }
    }
}
