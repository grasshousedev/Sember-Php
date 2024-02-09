<?php

namespace Asko\Nth\Controllers;

use Asko\Nth\DB;
use Asko\Nth\Models\Post;
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
            ->where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->map(function (Post $post) {
                $post->set('html', $post->renderHtml());

                return $post;
            })
            ->toArray();

        return $response->view('site/home', [
            'posts' => $posts,
        ]);
    }

    public function notFound(Response $response): Response
    {
        return $response->view('site/404');
    }
}