<?php

namespace Asko\Sember\Middlewares\Route;

use Asko\Sember\Database;
use Asko\Sember\Models\User;
use Asko\Sember\Request;
use Asko\Sember\Response;

readonly class IsNotAuthenticatedMiddleware
{
    public function __construct(private Database $db)
    {
    }

    public function handle(Request $request, Response $response): ?Response
    {
        $auth_token = $request->session()->get('auth_token');

        // If you are authenticated, redirect to posts page.
        if ($auth_token && $this->db->findOne(User::class, 'where auth_token = ?', [$auth_token])) {
            return $response->redirect('/admin/posts');
        }

        return null;
    }
}
