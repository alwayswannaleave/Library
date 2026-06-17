<?php

namespace App\Controllers;

use App\Core\Request;

class AuthController
{
    public function login(Request $request): array
    {
        return [
            'message' => 'Login endpoint - will be implemented',
            'status' => 'ok',
        ];
    }

    public function register(Request $request): array
    {
        return [
            'message' => 'Register endpoint - will be implemented',
            'status' => 'ok',
        ];
    }
}
