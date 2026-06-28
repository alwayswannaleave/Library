<?php

namespace App\Services;

use App\Models\Book;
use App\Repositories\BookRepository;

class BookService
{
    private BookRepository $bookRepository;
    private FileService $fileService;

    public function __construct()
    {
        $this->bookRepository = new BookRepository();
        $this->fileService = new FileService();
    }

    public function createBook(int $userId, string $title, ?string $content, ?array $file): array
    {
        if (empty($title)) {
            return ['error' => 'Title is required'];
        }

        if (empty($content) && $file) {
            $validation = $this->fileService->validateFile($file);
            if (isset($validation['error'])) {
                return $validation;
            }
            $content = $this->fileService->parseUploadedFile($file);
        }

        if (empty($content)) {
            return ['error' => 'Book content is required (text or file)'];
        }

        $book = new Book($userId, $title, $content);
        $saved = $this->bookRepository->save($book);

        if (!$saved) {
            return ['error' => 'Failed to save book'];
        }

        return [
            'message' => 'Book created successfully',
            'book' => [
                'id' => $book->getId(),
                'title' => $book->getTitle(),
            ],
        ];
    }

    public function getUserBooks(int $userId): array
    {
        $books = $this->bookRepository->findByUserId($userId);
        $result = [];

        foreach ($books as $book) {
            $result[] = [
                'id' => $book->getId(),
                'title' => $book->getTitle(),
                'created_at' => $book->getCreatedAt(),
            ];
        }

        return ['books' => $result];
    }

    public function getBook(int $id, int $userId): array
    {
        $book = $this->bookRepository->findById($id);

        if (!$book) {
            return ['error' => 'Book not found'];
        }

        if ($book->getUserId() !== $userId) {
            return ['error' => 'Access denied'];
        }

        return [
            'book' => [
                'id' => $book->getId(),
                'title' => $book->getTitle(),
                'content' => $book->getContent(),
                'created_at' => $book->getCreatedAt(),
                'updated_at' => $book->getUpdatedAt(),
            ],
        ];
    }

    public function updateBook(int $id, int $userId, string $title, string $content): array
    {
        if (empty($title) || empty($content)) {
            return ['error' => 'Title and content are required'];
        }

        $book = $this->bookRepository->findById($id);

        if (!$book) {
            return ['error' => 'Book not found'];
        }

        if ($book->getUserId() !== $userId) {
            return ['error' => 'Access denied'];
        }

        $book->setTitle($title);
        $book->setContent($content);

        $updated = $this->bookRepository->update($book);

        if (!$updated) {
            return ['error' => 'Failed to update book'];
        }

        return ['message' => 'Book updated successfully'];
    }

    public function deleteBook(int $id, int $userId): array
    {
        $book = $this->bookRepository->findById($id);

        if (!$book) {
            return ['error' => 'Book not found'];
        }

        if ($book->getUserId() !== $userId) {
            return ['error' => 'Access denied'];
        }

        $deleted = $this->bookRepository->softDelete($id, $userId);

        if (!$deleted) {
            return ['error' => 'Failed to delete book'];
        }

        return ['message' => 'Book deleted successfully'];
    }

    public function restoreBook(int $id, int $userId): array
    {
        $restored = $this->bookRepository->restore($id, $userId);

        if (!$restored) {
            return ['error' => 'Book not found or already restored'];
        }

        return ['message' => 'Book restored successfully'];
    }

    public function getUserBooksById(int $ownerUserId, int $currentUserId): array
    {
        if ($ownerUserId === $currentUserId) {
            $books = $this->bookRepository->findByUserId($ownerUserId);
            return $this->formatBooksResponse($books);
        }

        $accessRepo = new \App\Repositories\AccessRepository();
        if (!$accessRepo->hasAccess($ownerUserId, $currentUserId)) {
            return ['error' => 'Access denied'];
        }

        $books = $this->bookRepository->findByUserId($ownerUserId);
        return $this->formatBooksResponse($books);
    }

    private function formatBooksResponse(array $books): array
    {
        $result = [];
        foreach ($books as $book) {
            $result[] = [
                'id' => $book->getId(),
                'title' => $book->getTitle(),
                'created_at' => $book->getCreatedAt(),
            ];
        }
        return ['books' => $result];
    }
}
