<?php
/**
 * Skeleton application for SimpleMVC
 * 
 * @link      http://github.com/simplemvc/skeleton
 * @copyright Copyright (c) Enrico Zimuel (https://www.zimuel.it)
 * @license   https://opensource.org/licenses/MIT MIT License
 */
declare(strict_types=1);

namespace App\Service;

use App\Model\User;
use PDO;

class Auth
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Verify the credential of an active user
     */
    public function verifyUsername(string $username, string $password): bool
    {
        $sth = $this->pdo->prepare('SELECT * FROM users WHERE username = :username AND active=1');
        $sth->bindParam(':username', $username);
        $sth->execute();
        $user = $sth->fetchObject(User::class);
        if (empty($user) || false === password_verify($password, $user->password)) {
            return false;
        }
        return true;
    }
}