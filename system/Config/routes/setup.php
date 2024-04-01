<?php

use Sember\System\Controllers\SetupController;
use Sember\System\Middlewares\Route\CSRFMiddleware;
use Sember\System\Middlewares\Route\RequiresNotSetupMiddleware;

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