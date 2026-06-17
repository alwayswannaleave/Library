<?php

use App\Controllers\AuthController;

return [
    [
        'method' => 'POST',
        'path' => '/auth/login',
        'controller' => AuthController::class,
        'action' => 'login',
    ],
    [
        'method' => 'POST',
        'path' => '/auth/register',
        'controller' => AuthController::class,
        'action' => 'register',
    ],
    [
        'method' => 'GET',
        'path' => '/test',
        'controller' => AuthController::class,
        'action' => 'login',
    ],
];
