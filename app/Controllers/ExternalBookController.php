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
            http_response_code(401);
            return ['error' => 'Unauthorized'];
        }

        $query = $request->get('q');
        if (empty($query)) {
            http_response_code(400);
            return ['error' => 'Search query is required'];
        }

        $result = $this->externalBookService->searchBooks($query);

        if (isset($result['error'])) {
            http_response_code(404);
            return $result;
        }

        return $result;
    }

    public function save(Request $request): array
    {
        $userId = $this->getUserIdFromToken($request);
        if (!$userId) {
            http_response_code(401);
            return ['error' => 'Unauthorized'];
        }

        $externalId = $request->getParam('id');
        if (empty($externalId)) {
            http_response_code(400);
            return ['error' => 'Book ID is required'];
        }

        $data = $request->all();
        $searchResults = $data['search_results'] ?? [];

        if (empty($searchResults)) {
            http_response_code(400);
            return ['error' => 'Search results are required'];
        }

        $result = $this->externalBookService->saveBook($userId, $externalId, $searchResults);

        if (isset($result['error'])) {
            http_response_code(400);
            return $result;
        }

        return $result;
    }
}
