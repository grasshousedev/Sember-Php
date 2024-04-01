<?php

namespace Sember\System\Middlewares\Route;

use Sember\System\Database;
use Sember\System\Models\User;
use Sember\System\Request;
use Sember\System\Response;

readonly class IsNotAuthenticatedMiddleware
{
    public function __construct(private Database $db)
    {
    }

    public function handle(Request $request, Response $response): ?Response
    {
        $auth_token = $request->cookie()->get('auth_token');

        // If you are authenticated, redirect to posts page.
        if ($auth_token && $this->db->findOne(User::class, 'where auth_token = ?', [$auth_token])) {
            return $response->redirect('/admin/posts');
        }

        return null;
    }
}
