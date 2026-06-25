<?php

namespace App\Services;

use App\Helpers\JwtHelper;
use App\Models\User;
use App\Repositories\UserRepository;

class AuthService
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function register(string $login, string $password, string $passwordConfirmation): array
    {
        if (empty($login) || empty($password)) {
            return ['error' => 'Login and password are required'];
        }

        if ($password !== $passwordConfirmation) {
            return ['error' => 'Passwords do not match'];
        }

        if (strlen($password) < 6) {
            return ['error' => 'The password must contain at least 6 characters.'];
        }

        $existingUser = $this->userRepository->findByLogin($login);
        if ($existingUser) {
            return ['error' => 'Login already exists'];
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        $user = new User($login, $passwordHash);
        $saved = $this->userRepository->save($user);

        if (!$saved) {
            return ['error' => 'Failed to save user'];
        }

        return ['message' => 'User registered successfully'];
    }

    public function login(string $login, string $password): array
    {
        if (empty($login) || empty($password)) {
            return ['error' => 'Login and password are required'];
        }

        $user = $this->userRepository->findByLogin($login);
        if (!$user) {
            return ['error' => 'Invalid credentials'];
        }

        if (!password_verify($password, $user->getPasswordHash())) {
            return ['error' => 'Invalid credentials'];
        }

        $token = JwtHelper::generate($user->getId());

        return [
            'message' => 'Login successful',
            'token' => $token,
        ];
    }
}
