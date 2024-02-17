<?php

use Asko\Sember\Controllers\AdminAPIController;
use Asko\Sember\Middlewares\Route\IsAuthenticatedMiddleware;
use Asko\Sember\Middlewares\Route\RequiresSetupMiddleware;

return [
    [
        'method' => 'get',
        'path' => '/admin/api/post/{id}/editor',
        'callable' => [AdminAPIController::class, 'editor'],
        'middleware' => [RequiresSetupMiddleware::class, IsAuthenticatedMiddleware::class]
    ],
    [
        'method' => 'get',
        'path' => '/admin/api/post/{id}/status',
        'callable' => [AdminAPIController::class, 'status'],
        'middleware' => [RequiresSetupMiddleware::class, IsAuthenticatedMiddleware::class]
    ],
    [
        'method' => 'get',
        'path' => '/admin/api/post/{id}/published-at',
        'callable' => [AdminAPIController::class, 'publishedAt'],
        'middleware' => [RequiresSetupMiddleware::class, IsAuthenticatedMiddleware::class]
    ],
    [
        'method' => 'post',
        'path' => '/admin/api/post/{id}/update-title',
        'callable' => [AdminAPIController::class, 'updateTitle'],
        'middleware' => [RequiresSetupMiddleware::class, IsAuthenticatedMiddleware::class]
    ],
    [
        'method' => 'post',
        'path' => '/admin/api/post/{id}/update-slug',
        'callable' => [AdminAPIController::class, 'updateSlug'],
        'middleware' => [RequiresSetupMiddleware::class, IsAuthenticatedMiddleware::class]
    ],
    [
        'method' => 'post',
        'path' => '/admin/api/post/{id}/update-status',
        'callable' => [AdminAPIController::class, 'updateStatus'],
        'middleware' => [RequiresSetupMiddleware::class, IsAuthenticatedMiddleware::class]
    ],
    [
        'method' => 'post',
        'path' => '/admin/api/post/{id}/update-published-at',
        'callable' => [AdminAPIController::class, 'updatePublishedAt'],
        'middleware' => [RequiresSetupMiddleware::class, IsAuthenticatedMiddleware::class]
    ],
    [
        'method' => 'post',
        'path' => '/admin/api/post/{id}/blocks/add/{type}/{position}',
        'callable' => [AdminAPIController::class, 'addBlock'],
        'middleware' => [RequiresSetupMiddleware::class, IsAuthenticatedMiddleware::class]
    ],
    [
        'method' => 'post',
        'path' => '/admin/api/post/{id}/blocks/{blockId}',
        'callable' => [AdminAPIController::class, 'updateBlock'],
        'middleware' => [RequiresSetupMiddleware::class, IsAuthenticatedMiddleware::class]
    ],
    [
        'method' => 'delete',
        'path' => '/admin/api/post/{id}/blocks/{blockId}',
        'callable' => [AdminAPIController::class, 'deleteBlock'],
        'middleware' => [RequiresSetupMiddleware::class, IsAuthenticatedMiddleware::class]
    ],
    [
        'method' => 'post',
        'path' => '/admin/api/post/{id}/blocks/{blockId}/move/{direction}',
        'callable' => [AdminAPIController::class, 'moveBlock'],
        'middleware' => [RequiresSetupMiddleware::class, IsAuthenticatedMiddleware::class]
    ],
    [
        'method' => 'post',
        'path' => '/admin/api/post/{id}/blocks/{blockId}/opt/{fn}',
        'callable' => [AdminAPIController::class, 'blockOption'],
        'middleware' => [RequiresSetupMiddleware::class, IsAuthenticatedMiddleware::class]
    ],
    [
        'method' => 'post',
        'path' => '/admin/api/post/{id}/blocks/{blockId}/opt/{fn}/{arg}',
        'callable' => [AdminAPIController::class, 'blockOption'],
        'middleware' => [RequiresSetupMiddleware::class, IsAuthenticatedMiddleware::class]
    ],
    [
        'method' => 'post',
        'path' => '/admin/api/settings/update-site-name',
        'callable' => [AdminAPIController::class, 'updateSiteName'],
        'middleware' => [RequiresSetupMiddleware::class, IsAuthenticatedMiddleware::class]
    ],
    [
        'method' => 'post',
        'path' => '/admin/api/settings/update-site-description',
        'callable' => [AdminAPIController::class, 'updateSiteDescription'],
        'middleware' => [RequiresSetupMiddleware::class, IsAuthenticatedMiddleware::class]
    ]
];