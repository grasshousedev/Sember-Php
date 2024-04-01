<?php

use Asko\Sember\Controllers\AdminAPIController;
use Asko\Sember\Controllers\AdminController;
use Asko\Sember\Controllers\AuthenticationController;
use Asko\Sember\Controllers\SetupController;
use Asko\Sember\Controllers\SiteController;
use Asko\Sember\Middlewares\Route\IsAuthenticatedMiddleware;
use Asko\Sember\Middlewares\Route\RequiresNotSetupMiddleware;
use Asko\Sember\Middlewares\Route\RequiresSetupMiddleware;

return function (\Asko\Router\Router $router) {
    $routes = [
        ...require(__DIR__ . '/routes/admin.php'),
        ...require(__DIR__ . '/routes/admin-api.php'),
        ...require(__DIR__ . '/routes/setup.php'),
        ...require(SEMBER_ROOT . '/app/routes.php')
    ];

    // $router->read(__DIR__ . '/routes/admin.php'); // TODO: Implement this

    foreach ($routes as $route) {
        if (isset($route['path'])) {
            call_user_func([$router, $route['method']], $route['path'], $route['callable']);
        } else {
            call_user_func([$router, $route['method']], $route['callable']);
        }

        if (isset($route['middleware'])) {
            call_user_func([$router, 'middleware'], $route['middleware']);
        }
    }
};