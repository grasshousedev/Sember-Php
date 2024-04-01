<?php

use Sember\System\Controllers\AdminController;
use Sember\System\Controllers\AuthenticationController;
use Sember\System\Middlewares\Route\IsAuthenticatedMiddleware;
use Sember\System\Middlewares\Route\IsNotAuthenticatedMiddleware;
use Sember\System\Middlewares\Route\RequiresSetupMiddleware;

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
        'middleware' => [RequiresSetupMiddleware::class, IsNotAuthenticatedMiddleware::class]
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
    ],
    [
        'method' => 'get',
        'path' => '/admin/settings',
        'callable' => [AdminController::class, 'settings'],
        'middleware' => [RequiresSetupMiddleware::class, IsAuthenticatedMiddleware::class]
    ]
];