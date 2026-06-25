<?php

namespace App\Controllers;

use App\Core\Request;
use App\Services\AuthService;

class AuthController
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function register(Request $request): array
    {
        $data = $request->all();

        $login = $data['login'] ?? '';
        $password = $data['password'] ?? '';
        $passwordConfirmation = $data['password_confirmation'] ?? '';

        $result = $this->authService->register($login, $password, $passwordConfirmation);

        if (isset($result['error'])) {
            http_response_code(400);
            return $result;
        }

        return $result;
    }

    public function login(Request $request): array
    {
        $data = $request->all();

        $login = $data['login'] ?? '';
        $password = $data['password'] ?? '';

        $result = $this->authService->login($login, $password);

        if (isset($result['error'])) {
            http_response_code(401);
            return $result;
        }

        return $result;
    }
}
