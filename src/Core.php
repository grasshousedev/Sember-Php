<?php

namespace Asko\Nth;

use Asko\Router\Router;
use Exception;

class Core
{
    /**
     * @throws Exception
     */
    public static function init(): void
    {
        // Start session.
        session_start();

        // Route the request.
        if (!file_exists(__DIR__ . '/Config/routes.php')) {
            throw new Exception('Routes file not found.');
        }

        // Middlewares
        if (!file_exists(__DIR__ . '/Config/middlewares.php')) {
            throw new Exception('Middlewares file not found.');
        }

        $middlewares = require __DIR__ . '/Config/middlewares.php';

        // Before middlewares
        foreach ($middlewares as $middleware) {
            if (method_exists($middleware, 'before')) {
                if ($response = call_user_func([$middleware, 'before'])) {
                    echo $response->send();
                    return;
                }
            }
        }

        // Request routing
        $router = new Router();
        $routes = require __DIR__ . '/Config/routes.php';
        $routes($router);
        $response = $router->dispatch();

        if ($response instanceof Response) {
            echo $response->send();
        }

        // After middlewares
        foreach ($middlewares as $middleware) {
            if (method_exists($middleware, 'after')) {
                call_user_func([$middleware, 'after']);
            }
        }
    }

}