<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtHelper
{
    private static string $secret;
    private static int $expiresIn;

    public static function init(): void
    {
        self::$secret = $_ENV['JWT_SECRET'] ?? 'example-key';
        self::$expiresIn = (int) ($_ENV['JWT_EXPIRES_IN'] ?? 3600);
    }

    public static function generate(int $userId): string
    {
        self::init();

        $payload = [
            'user_id' => $userId,
            'exp' => time() + self::$expiresIn,
        ];

        return JWT::encode($payload, self::$secret, 'HS256');
    }

    public static function validate(string $token): ?int
    {
        self::init();

        try {
            $decoded = JWT::decode($token, new Key(self::$secret, 'HS256'));
            return (int) $decoded->user_id;
        } catch (\Exception $e) {
            return null;
        }
    }
}
