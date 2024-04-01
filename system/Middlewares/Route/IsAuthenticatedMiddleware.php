<?php

namespace Sember\System\Middlewares\Route;

use Sember\System\Database;
use Sember\System\Models\User;
use Sember\System\Request;
use Sember\System\Response;

readonly class IsAuthenticatedMiddleware
{
    public function __construct(private Database $db)
    {
    }

    public function handle(Request $request, Response $response): ?Response
    {
        $auth_token = $request->cookie()->get('auth_token');

        // If there is no auth token, redirect to sign in page.
        if (!$auth_token) {
            return $response->redirect('/admin/signin');
        }

        // If the user with the auth token does not exist, redirect to sign in page.
        if (!$this->db->findOne(User::class, 'where auth_token = ?', [$auth_token])) {
            return $response->redirect('/admin/signin');
        }

        return null;
    }
}
