<?php

namespace App\Controllers;

use App\Core\Request;
use App\Helpers\JwtHelper;
use App\Services\ExternalBookService;

class ExternalBookController
{
    private ExternalBookService $externalBookService;

    public function __construct()
    {
        $this->externalBookService = new ExternalBookService();
    }

    private function getUserIdFromToken(Request $request): ?int
    {
        $authHeader = $request->getHeader('Authorization');
        if (!$authHeader || !preg_match('/Bearer\s+(.*)/', $authHeader, $matches)) {
            return null;
        }
        return JwtHelper::validate($matches[1]);
    }

    public function search(Request $request): array
    {
        $userId = $this->getUserIdFromToken($request);
        if (!$userId) {
           throw new \Exception('Unauthorized', 401);
        }

        $query = $request->get('q');
        if (empty($query)) {
            throw new \Exception('Search query is required', 400);
        }

        $result = $this->externalBookService->searchBooks($query);

        if (isset($result['error'])) {
            throw new \Exception($result['error'], 404);
        }

        return $result;
    }

    public function save(Request $request): array
    {
        $userId = $this->getUserIdFromToken($request);
        if (!$userId) {
            throw new \Exception('Unauthorized', 401);
        }

        $externalId = $request->getParam('id');
        if (empty($externalId)) {
            throw new \Exception('Book ID is required', 400);
        }

        $data = $request->all();
        $searchResults = $data['search_results'] ?? [];

        if (empty($searchResults)) {
            throw new \Exception('Search results are required', 400);
        }

        $result = $this->externalBookService->saveBook($userId, $externalId, $searchResults);

        if (isset($result['error'])) {
            throw new \Exception($result['error'], 400);
        }

        return $result;
    }
}
