<?php

use Sember\App\Controllers\SiteController;
use Sember\System\Middlewares\Route\RequiresSetupMiddleware;

return [
    [
        'method' => 'get',
        'path' => '/',
        'callable' => [SiteController::class, 'home'],
        'middleware' => RequiresSetupMiddleware::class
    ],
    [
        'method' => 'get',
        'path' => '/page/{page}',
        'callable' => [SiteController::class, 'home'],
        'middleware' => RequiresSetupMiddleware::class,
    ],
    [
        'method' => 'get',
        'path' => '/{slug}',
        'callable' => [SiteController::class, 'post'],
        'middleware' => RequiresSetupMiddleware::class
    ],
    [
        'method' => 'notFound',
        'callable' => [SiteController::class, 'notFound'],
    ]
];