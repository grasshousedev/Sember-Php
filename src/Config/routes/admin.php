<?php

use Asko\Sember\Controllers\AdminController;
use Asko\Sember\Controllers\AuthenticationController;
use Asko\Sember\Middlewares\Route\IsAuthenticatedMiddleware;
use Asko\Sember\Middlewares\Route\RequiresSetupMiddleware;

return [
    [
        'method' => 'get',
        'path' => '/admin',
        'callable' => [AdminController::class, 'index'],
        'middleware' => RequiresSetupMiddleware::class
    ],
    [
        'method' => 'get',
        'path' => '/admin/signin',
        'callable' => [AuthenticationController::class, 'signIn'],
        'middleware' => RequiresSetupMiddleware::class
    ],
    [
        'method' => 'post',
        'path' => '/admin/signin',
        'callable' => [AuthenticationController::class, 'signIn'],
        'middleware' => RequiresSetupMiddleware::class
    ],
    [
        'method' => 'get',
        'path' => '/admin/signout',
        'callable' => [AdminController::class, 'signOut'],
        'middleware' => [RequiresSetupMiddleware::class, IsAuthenticatedMiddleware::class]
    ],
    [
        'method' => 'get',
        'path' => '/admin/posts',
        'callable' => [AdminController::class, 'posts'],
        'middleware' => [RequiresSetupMiddleware::class, IsAuthenticatedMiddleware::class]
    ],
    [
        'method' => 'get',
        'path' => '/admin/posts/new',
        'callable' => [AdminController::class, 'createPost'],
        'middleware' => [RequiresSetupMiddleware::class, IsAuthenticatedMiddleware::class]
    ],
    [
        'method' => 'get',
        'path' => '/admin/posts/edit/{id}',
        'callable' => [AdminController::class, 'editPost'],
        'middleware' => [RequiresSetupMiddleware::class, IsAuthenticatedMiddleware::class]
    ],
    [
        'method' => 'get',
        'path' => '/admin/posts/delete/{id}',
        'callable' => [AdminController::class, 'deletePost'],
        'middleware' => [RequiresSetupMiddleware::class, IsAuthenticatedMiddleware::class]
    ]
];