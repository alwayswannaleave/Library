<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\Book;
use PDO;

class BookRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function save(Book $book): bool
    {
        $stmt = $this->db->prepare('
            INSERT INTO books (user_id, title, content)
            VALUES (:user_id, :title, :content)
        ');

        return $stmt->execute([
            'user_id' => $book->getUserId(),
            'title' => $book->getTitle(),
            'content' => $book->getContent(),
        ]);
    }

    public function findById(int $id): ?Book
    {
        $stmt = $this->db->prepare('
            SELECT * FROM books WHERE id = :id AND deleted_at IS NULL
        ');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return new Book(
            (int) $row['user_id'],
            $row['title'],
            $row['content'],
            (int) $row['id'],
            $row['created_at'],
            $row['updated_at'],
            $row['deleted_at']
        );
    }

    public function findByUserId(int $userId): array
    {
        $stmt = $this->db->prepare('
            SELECT * FROM books
            WHERE user_id = :user_id AND deleted_at IS NULL
            ORDER BY id DESC
        ');
        $stmt->execute(['user_id' => $userId]);

        $books = [];
        foreach ($stmt->fetchAll() as $row) {
            $books[] = new Book(
                (int) $row['user_id'],
                $row['title'],
                $row['content'],
                (int) $row['id'],
                $row['created_at'],
                $row['updated_at'],
                $row['deleted_at']
            );
        }
        return $books;
    }

    public function update(Book $book): bool
    {
        $stmt = $this->db->prepare('
            UPDATE books
            SET title = :title, content = :content
            WHERE id = :id AND user_id = :user_id AND deleted_at IS NULL
        ');

        return $stmt->execute([
            'id' => $book->getId(),
            'user_id' => $book->getUserId(),
            'title' => $book->getTitle(),
            'content' => $book->getContent(),
        ]);
    }

    public function softDelete(int $id, int $userId): bool
    {
        $stmt = $this->db->prepare('
            UPDATE books
            SET deleted_at = NOW()
            WHERE id = :id AND user_id = :user_id AND deleted_at IS NULL
        ');

        return $stmt->execute(['id' => $id, 'user_id' => $userId]);
    }

    public function restore(int $id, int $userId): bool
    {
        $stmt = $this->db->prepare('
            UPDATE books
            SET deleted_at = NULL
            WHERE id = :id AND user_id = :user_id AND deleted_at IS NOT NULL
        ');

        return $stmt->execute(['id' => $id, 'user_id' => $userId]);
    }
}
