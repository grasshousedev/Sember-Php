<?php

namespace Asko\Sember\Middlewares\Route;

use Asko\Sember\DB;
use Asko\Sember\Models\Meta;
use Asko\Sember\Models\User;
use Asko\Sember\Response;

class RequiresNotSetupMiddleware
{
    public function handle(Response $response): ?Response
    {
        $user = DB::find(User::class, ['role' => 'admin']);
        $site_meta = DB::find(Meta::class, ['meta_name' => 'site_config']);

        if ($user && $site_meta) {
            return $response->redirect('/admin');
        }

        return null;
    }
}