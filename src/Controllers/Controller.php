<?php

namespace Asko\Sember\Controllers;

use Asko\Sember\DB;
use Asko\Sember\Models\Meta;
use Asko\Sember\Models\User;
use Asko\Sember\Request;
use Asko\Sember\Response;
use Exception;

class Controller
{
    /**
     * Setup guard.
     *
     * @return void
     */
    public function setupGuard(): void
    {
        $user = DB::find(User::class, ['role' => 'admin']);
        $site_meta = DB::find(Meta::class, ['meta_name' => 'site_config']);

        if (!$user || !$site_meta) {
            (new Response)->redirect('/setup/account')->send();
        }
    }

    /**
     * Not setup guard.
     *
     * @return void
     */
    public function notSetupGuard(): void
    {
        $user = DB::find(User::class, ['role' => 'admin']);
        $site_meta = DB::find(Meta::class, ['meta_name' => 'site_config']);

        if ($user && $site_meta) {
            (new Response)->redirect('/admin')->send();
        }
    }

    public function authenticatedGuard(): void
    {
        $auth_token = (new Request)->session()->get('auth_token');

        // If there is no auth token, redirect to sign in page.
        if (!$auth_token) {
            (new Response)->redirect('/admin/signin')->send();
            return;
        }

        // If the user with the auth token does not exist, redirect to sign in page.
        if (!DB::find(User::class, ['auth_token' => $auth_token])) {
            (new Response)->redirect('/admin/signin')->send();
        }
    }
}