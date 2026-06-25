<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\User;
use PDO;

class UserRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function findByLogin(string $login): ?User
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE login = :login');
        $stmt->execute(['login' => $login]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return new User(
            $row['login'],
            $row['password_hash'],
            (int) $row['id'],
            $row['created_at']
        );
    }

    public function save(User $user): bool
    {
        $stmt = $this->db->prepare('
            INSERT INTO users (login, password_hash, created_at) VALUES (:login, :password_hash, NOW())
        ');

        return $stmt->execute([
        'login' => $user->getLogin(),
        'password_hash' => $user->getPasswordHash(),
        ]);
    }

    public function findById(int $id): ?User
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return new User(
            $row['login'],
            $row['password_hash'],
            (int) $row['id'],
            $row['created_at']
        );
    }

    public function findAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM users ORDER BY id');
        $users = [];

        foreach ($stmt->fetchAll() as $row) {
            $users[] = new User(
                $row['login'],
                $row['password_hash'],
                (int) $row['id'],
                $row['created_at']
            );
        }

        return $users;
    }
}
