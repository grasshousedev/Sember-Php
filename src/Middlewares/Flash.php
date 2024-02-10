<?php

namespace Asko\Sember\Middlewares;

use Asko\Sember\Request;

class Flash
{
    public static function after(): void
    {
        $request = new Request();
        $flash = $request->session()->get('flash');
        $flash_hits = $request->session()->get('flash_hits') ?? 1;

        // Flash is set and has been shown once already.
        if ($flash && $flash_hits > 1) {
            $request->session()->remove('flash');
            $request->session()->remove('flash_hits');

            return;
        }

        // Flash is set and has not been shown yet.
        if ($flash) {
            $request->session()->set('flash_hits', $flash_hits + 1);
        }
    }
}