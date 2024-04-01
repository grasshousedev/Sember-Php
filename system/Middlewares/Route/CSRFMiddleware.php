<?php

namespace Sember\System\Middlewares\Route;

use Sember\System\Request;

class CSRFMiddleware
{
    public function handle(Request $request): void
    {
        $session_csrf_token = $request->session()->get('csrf_token');
        $body_csrf_token = $request->input('csrf_token') ?? $request->headers('X-Csrftoken');

        if ($session_csrf_token !== $body_csrf_token) {
            die("CSRF token mismatch.");
        }

        // All good, reset the token.
        if (!$_SERVER['HTTP_X_REQUESTED_WITH']) {
            unset($_SESSION['csrf_token']);
        }
    }
}