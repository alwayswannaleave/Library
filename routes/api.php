<?php

use App\Controllers\AuthController;
use App\Controllers\UserController;

return [
    [
        'method' => 'POST',
        'path' => '/auth/register',
        'controller' => AuthController::class,
        'action' => 'register',
    ],
    [
        'method' => 'POST',
        'path' => '/auth/login',
        'controller' => AuthController::class,
        'action' => 'login',
    ],

    [
        'method' => 'GET',
        'path' => '/users',
        'controller' => UserController::class,
        'action' => 'index',
    ],
    [
        'method' => 'POST',
        'path' => '/users/{id}/access',
        'controller' => UserController::class,
        'action' => 'grantAccess',
    ],
];
