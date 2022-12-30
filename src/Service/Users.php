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

use App\Exception\DatabaseException;
use App\Model\User;
use PDO;

class Users
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Returns a user by ID
     * @throws DatabaseException
     */
    public function get(int $id): User
    {
        $sth = $this->pdo->prepare('SELECT * FROM users WHERE id = :id');
        $sth->bindParam(':id', $id, PDO::PARAM_INT);
        $sth->execute();
        $result = $sth->fetchObject(User::class);
        if (false === $result) {
            throw new DatabaseException(sprintf(
                "The user with ID %d does not exist",
                $id
            ));
        }
        return $result;
    }
    
    /**
     * Returns all users
     * @return User[]
     */
    public function getAll(int $start, int $size): array
    {
        $sth = $this->pdo->prepare('SELECT * FROM users LIMIT :start, :size');
        $sth->bindParam(':start', $start, PDO::PARAM_INT);
        $sth->bindParam(':size', $size, PDO::PARAM_INT);
        $sth->execute();
        return $sth->fetchAll(PDO::FETCH_CLASS, User::class);
    }

    /**
     * Delete a user with ID
     * @throws DatabaseException
     */
    public function delete(int $id): void
    {
        $sth = $this->pdo->prepare('DELETE FROM users WHERE id=:id');
        $sth->bindParam(":id", $id, PDO::PARAM_INT);
        if (!$sth->execute()) {
            throw new DatabaseException(sprintf(
                "Cannot delete ID %d: %s",
                $id,
                implode(',', $this->pdo->errorInfo())
            ));
        }
    }

    /**
     * Returns the total number of users
     */
    public function getTotalUsers(): int
    {
        $sth = $this->pdo->prepare('SELECT COUNT(*) AS tot FROM users');
        $sth->execute();
        return (int) $sth->fetch()['tot'];
    }

    /**
     * Returns true if the username already exist
     */
    public function exists(string $username): bool
    {
        $sth = $this->pdo->prepare('SELECT id FROM users WHERE username = :username');
        $sth->bindParam(':username', $username);
        if (!$sth->execute()) {
            return false;
        }
        $result = $sth->fetch(PDO::FETCH_ASSOC);
        return isset($result['id']);
    }

    /**
     * Create a new user with username and password
     * @throws DatabaseException
     */
    public function create(string $username, string $password): void
    {
        $sth = $this->pdo->prepare('INSERT INTO users (username, password) VALUES (:username, :password)');
        $sth->bindParam(':username', $username);
        $sth->bindValue(':password', password_hash($password, PASSWORD_DEFAULT));
        if (!$sth->execute()) {
            throw new DatabaseException(sprintf(
                "Cannot add username %s: %s",
                $username,
                implode(',', $this->pdo->errorInfo())
            ));
        }
    }

    /**
     * Update the user with active and password if not empty
     * @throws DatabaseException
     */
    public function update(int $id, bool $active, string $password): void
    {
        if (empty($password)) {
            $sth = $this->pdo->prepare('UPDATE users SET active = :active WHERE id = :id');
        } else {
            $sth = $this->pdo->prepare('UPDATE users SET active = :active, password = :password WHERE id = :id');
            $sth->bindValue(':password', password_hash($password, PASSWORD_DEFAULT));
        }
        $sth->bindParam(':id', $id, PDO::PARAM_INT);
        $sth->bindValue(':active', $active ? 1 : 0, PDO::PARAM_INT);
        if (!$sth->execute()) {
            throw new DatabaseException(sprintf(
                "Cannot update user id %d: %s",
                $id,
                implode(',', $this->pdo->errorInfo())
            ));
        }
    }

    /**
     * Update the last login with the actual time
     * @throws DatabaseException
     */
    public function updateLastLogin(string $username): void
    {
        $sth = $this->pdo->prepare('UPDATE users SET last_login = :last_login WHERE username = :username');
        $sth->bindValue(':last_login', date("Y-m-d H:i:s"));
        $sth->bindParam(':username', $username);
        if (!$sth->execute()) {
            throw new DatabaseException(sprintf(
                "Cannot update last login for user %s",
                $username
            ));
        }
    }
}