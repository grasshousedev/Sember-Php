<?php

namespace Asko\Sember\Middlewares\Global;

use Asko\Sember\Request;
use Asko\Sember\Response;

class CSRF
{
    public static function before(): ?Response
    {
        $request = new Request();

        if (!$request->isPost()) {
            return null;
        }

        // Validate token
        $session_csrf_token = $request->session()->get('csrf_token');
        $body_csrf_token = $request->input('csrf_token') ?? $request->headers('X-Csrftoken');

        if ($session_csrf_token !== $body_csrf_token) {
            return (new Response)->make("CSRF token mismatch.");
        }

        // All good, reset the token.
        if (!$request->isAjax()) {
            $request->session()->remove('csrf_token');
        }

        return null;
    }
}