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
            http_response_code(401);
            return ['error' => 'Unauthorized'];
        }

        return $this->bookService->getUserBooks($userId);
    }

    public function create(Request $request): array
    {
        $userId = $this->getUserIdFromToken($request);
        if (!$userId) {
            http_response_code(401);
            return ['error' => 'Unauthorized'];
        }

        $data = $request->all();
        $files = $request->getFiles();

        $title = $data['title'] ?? '';
        $content = $data['content'] ?? null;
        $file = $files['file'] ?? null;

        $result = $this->bookService->createBook($userId, $title, $content, $file);

        if (isset($result['error'])) {
            http_response_code(400);
            return $result;
        }

        return $result;
    }

    public function show(Request $request): array
    {
        $userId = $this->getUserIdFromToken($request);
        if (!$userId) {
            http_response_code(401);
            return ['error' => 'Unauthorized'];
        }

        $id = (int) $request->getParam('id');
        if (!$id) {
            http_response_code(400);
            return ['error' => 'Book ID is required'];
        }

        $result = $this->bookService->getBook($id, $userId);

        if (isset($result['error'])) {
            http_response_code(404);
            return $result;
        }

        return $result;
    }

    public function update(Request $request): array
    {
        $userId = $this->getUserIdFromToken($request);
        if (!$userId) {
            http_response_code(401);
            return ['error' => 'Unauthorized'];
        }

        $id = (int) $request->getParam('id');
        if (!$id) {
            http_response_code(400);
            return ['error' => 'Book ID is required'];
        }

        $data = $request->all();
        $title = $data['title'] ?? '';
        $content = $data['content'] ?? '';

        $result = $this->bookService->updateBook($id, $userId, $title, $content);

        if (isset($result['error'])) {
            http_response_code(400);
            return $result;
        }

        return $result;
    }

    public function delete(Request $request): array
    {
        $userId = $this->getUserIdFromToken($request);
        if (!$userId) {
            http_response_code(401);
            return ['error' => 'Unauthorized'];
        }

        $id = (int) $request->getParam('id');
        if (!$id) {
            http_response_code(400);
            return ['error' => 'Book ID is required'];
        }

        $result = $this->bookService->deleteBook($id, $userId);

        if (isset($result['error'])) {
            http_response_code(404);
            return $result;
        }

        return $result;
    }

    public function restore(Request $request): array
    {
        $userId = $this->getUserIdFromToken($request);
        if (!$userId) {
            http_response_code(401);
            return ['error' => 'Unauthorized'];
        }

        $id = (int) $request->getParam('id');
        if (!$id) {
            http_response_code(400);
            return ['error' => 'Book ID is required'];
        }

        $result = $this->bookService->restoreBook($id, $userId);

        if (isset($result['error'])) {
            http_response_code(404);
            return $result;
        }

        return $result;
    }
}
