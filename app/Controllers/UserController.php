<?php

namespace App\Controllers;

use App\Core\Request;
use App\Helpers\JwtHelper;
use App\Repositories\UserRepository;
use App\Services\AccessService;

class UserController
{
    private UserRepository $userRepository;
    private AccessService $accessService;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->accessService = new AccessService();
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

        $users = $this->userRepository->findAll();
        $result = [];

        foreach ($users as $user) {
            $result[] = [
                'id' => $user->getId(),
                'login' => $user->getLogin(),
            ];
        }

        return ['users' => $result];
    }

    public function grantAccess(Request $request): array
    {
        $userId = $this->getUserIdFromToken($request);
        if (!$userId) {
            throw new \Exception('Unauthorized', 401);
        }

        $grantedUserId = (int) $request->getParam('id');
        if (!$grantedUserId) {
            throw new \Exception('User ID is required', 400);
        }

        $result = $this->accessService->grantAccess($userId, $grantedUserId);

        if (isset($result['error'])) {
            throw new \Exception($result['error'], 400);

        }

        return $result;
    }
}
