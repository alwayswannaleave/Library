<?php

namespace App\Controllers;

use App\Core\Request;
use App\Helpers\JwtHelper;
use App\Services\BookService;

class BookController
{
    private BookService $bookService;

    public function __construct()
    {
        $this->bookService = new BookService();
    }

    private function getUserIdFromToken(Request $request): ?int
    {
        $authHeader = $request->getHeader('Authorization');
        if (!$authHeader || !preg_match('/Bearer\s+(.*)/', $authHeader, $matches)) {
            return null;
        }

        return JwtHelper::validate($matches[1]);
    }

    public function index(Request $request): array
    {
        $userId = $this->getUserIdFromToken($request);
        if (!$userId) {
            throw new \Exception('Unauthorized', 401);
        }

        return $this->bookService->getUserBooks($userId);
    }

    public function create(Request $request): array
    {
        $userId = $this->getUserIdFromToken($request);
        if (!$userId) {
            throw new \Exception('Unauthorized', 401);
        }

        $data = $request->all();
        $files = $request->getFiles();

        $title = $data['title'] ?? '';
        $content = $data['content'] ?? null;
        $file = $files['file'] ?? null;

        $result = $this->bookService->createBook($userId, $title, $content, $file);

        if (isset($result['error'])) {
            throw new \Exception($result['error'], 400);
        }

        return $result;
    }

    public function show(Request $request): array
    {
        $userId = $this->getUserIdFromToken($request);
        if (!$userId) {
            throw new \Exception('Unauthorized', 401);
        }

        $id = (int) $request->getParam('id');
        if (!$id) {
            throw new \Exception('Book ID is required', 400);
        }

        $result = $this->bookService->getBook($id, $userId);

        if (isset($result['error'])) {
            throw new \Exception($result['error'], 404);
        }

        return $result;
    }

    public function update(Request $request): array
    {
        $userId = $this->getUserIdFromToken($request);
        if (!$userId) {
            throw new \Exception('Unauthorized', 401);
        }

        $id = (int) $request->getParam('id');
        if (!$id) {
            throw new \Exception('Book ID is required', 400);
        }

        $data = $request->all();
        $title = $data['title'] ?? '';
        $content = $data['content'] ?? '';

        $result = $this->bookService->updateBook($id, $userId, $title, $content);

        if (isset($result['error'])) {
            throw new \Exception($result['error'], 400);
        }

        return $result;
    }

    public function delete(Request $request): array
    {
        $userId = $this->getUserIdFromToken($request);
        if (!$userId) {
            throw new \Exception('Unauthorized', 401);
        }

        $id = (int) $request->getParam('id');
        if (!$id) {
            throw new \Exception('Book ID is required', 400);
        }

        $result = $this->bookService->deleteBook($id, $userId);

        if (isset($result['error'])) {
            throw new \Exception($result['error'], 400);
        }

        return $result;
    }

    public function restore(Request $request): array
    {
        $userId = $this->getUserIdFromToken($request);
        if (!$userId) {
            throw new \Exception('Unauthorized', 401);
        }

        $id = (int) $request->getParam('id');
        if (!$id) {
            throw new \Exception('Book ID is required', 400);
        }

        $result = $this->bookService->restoreBook($id, $userId);

        if (isset($result['error'])) {
            throw new \Exception($result['error'], 400);
        }

        return $result;
    }

    public function getUserBooks(Request $request): array
    {
        $currentUserId = $this->getUserIdFromToken($request);
        if (!$currentUserId) {
            throw new \Exception('Unauthorized', 401);
        }

        $ownerUserId = (int) $request->getParam('id');
        if (!$ownerUserId) {
            throw new \Exception('Book ID is required', 400);
        }

        $result = $this->bookService->getUserBooksById($ownerUserId, $currentUserId);

        if (isset($result['error'])) {
            throw new \Exception($result['error'], 400);
        }

        return $result;
    }
}
