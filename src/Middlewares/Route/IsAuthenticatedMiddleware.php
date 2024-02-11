<?php

namespace Asko\Sember\Middlewares\Route;

use Asko\Sember\DB;
use Asko\Sember\Models\User;
use Asko\Sember\Request;
use Asko\Sember\Response;

class IsAuthenticatedMiddleware
{
    public function handle(Response $response): ?Response
    {
        $auth_token = (new Request)->session()->get('auth_token');

        // If there is no auth token, redirect to sign in page.
        if (!$auth_token) {
            return $response->redirect('/admin/signin');
        }

        // If the user with the auth token does not exist, redirect to sign in page.
        if (!DB::find(User::class, ['auth_token' => $auth_token])) {
           return $response->redirect('/admin/signin');
        }

        return null;
    }
}
