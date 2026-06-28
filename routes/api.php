<?php

use App\Controllers\AuthController;
use App\Controllers\BookController;
use App\Controllers\ExternalBookController;
use App\Controllers\UserController;

return [
    //Auth
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

    //Users
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

    //Books
    [
        'method' => 'GET',
        'path' => '/users/{id}/books',
        'controller' => BookController::class,
        'action' => 'getUserBooks',
    ],
    [
        'method' => 'GET',
        'path' => '/books',
        'controller' => BookController::class,
        'action' => 'index',
    ],
    [
        'method' => 'POST',
        'path' => '/books',
        'controller' => BookController::class,
        'action' => 'create',
    ],
    [
        'method' => 'GET',
        'path' => '/books/{id}',
        'controller' => BookController::class,
        'action' => 'show',
    ],
    [
        'method' => 'PUT',
        'path' => '/books/{id}',
        'controller' => BookController::class,
        'action' => 'update',
    ],
    [
        'method' => 'DELETE',
        'path' => '/books/{id}',
        'controller' => BookController::class,
        'action' => 'delete',
    ],
    [
        'method' => 'POST',
        'path' => '/books/{id}/restore',
        'controller' => BookController::class,
        'action' => 'restore',
    ],

    //External books
    [
        'method' => 'GET',
        'path' => '/external/books',
        'controller' => ExternalBookController::class,
        'action' => 'search',
    ],
    [
        'method' => 'POST',
        'path' => '/external/books/{id}/save',
        'controller' => ExternalBookController::class,
        'action' => 'save',
    ],
];
