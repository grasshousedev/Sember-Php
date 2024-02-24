<?php

use Asko\Sember\Controllers\SetupController;
use Asko\Sember\Middlewares\Route\CSRFMiddleware;
use Asko\Sember\Middlewares\Route\RequiresNotSetupMiddleware;

return [
    [
        'method' => 'get',
        'path' => '/setup/account',
        'callable' => [SetupController::class, 'account'],
        'middleware' => RequiresNotSetupMiddleware::class
    ],
    [
        'method' => 'post',
        'path' => '/setup/account',
        'callable' => [SetupController::class, 'createAccount'],
        'middleware' => [RequiresNotSetupMiddleware::class, CSRFMiddleware::class]
    ],
    [
        'method' => 'get',
        'path' => '/setup/site',
        'callable' => [SetupController::class, 'site'],
        'middleware' => RequiresNotSetupMiddleware::class
    ],
    [
        'method' => 'post',
        'path' => '/setup/site',
        'callable' => [SetupController::class, 'site'],
        'middleware' => [RequiresNotSetupMiddleware::class, CSRFMiddleware::class]
    ]
];