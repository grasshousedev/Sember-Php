<?php

namespace Asko\Sember;

use Asko\Router\Router;
use Asko\Sember\Models\Migration;
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

        // Do not route files
        if (str_contains($_SERVER['REQUEST_URI'], '.')) {
            return;
        }

        // Files
        if (!is_dir(NTH_ROOT . '/storage/files')) {
            mkdir(NTH_ROOT . '/storage/files');
        }

        if (!is_link(NTH_ROOT . '/public/files')) {
            symlink(NTH_ROOT . '/storage/files', NTH_ROOT . '/public/files');
        }

        // Run migrations
        $db = new Database();
        $executed_migrations = $db->find(Migration::class)
            ->map(fn(Migration $m) => $m->get('migration'))
            ->toArray();

        $migrations_to_run = (new Collection(Config::get('migrations')))
            ->filter(fn($migration) => !in_array($migration, $executed_migrations))
            ->toArray();

        foreach ($migrations_to_run as $migration) {
            $migration_instance = new $migration($db);
            $migration_instance->up();

            $db->create(new Migration([
                'migration' => $migration,
                'created_at' => time(),
            ]));
        }

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