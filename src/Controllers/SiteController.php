<?php

namespace Asko\Nth\Controllers;

use Asko\Nth\DB;
use Asko\Nth\Models\Post;
use Asko\Nth\Models\User;
use Asko\Nth\Response;
use Exception;

class SiteController
{
    /**
     * @throws Exception
     */
    public function home(Response $response): Response
    {
        $posts = DB::findAll(Post::class)
            ->where('status', 'public')
            ->orderBy('created_at', 'desc')
            ->toArray();

        return $response->view('site/home', [
            'posts' => $posts,
        ]);
    }
}