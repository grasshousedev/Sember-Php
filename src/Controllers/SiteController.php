<?php

namespace Asko\Nth\Controllers;

use Asko\Nth\DB;
use Asko\Nth\Models\Post;
use Asko\Nth\Response;

class SiteController
{

    /**
     * Shows the home page.
     *
     * @param Response $response
     * @return Response
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

    /**
     * Shows a post.
     *
     * @param Response $response
     * @param string $slug
     * @return Response
     */
    public function post(Response $response, string $slug): Response
    {
        $post = DB::find(Post::class, ['slug' => $slug]);

        if (!$post) {
            return $this->notFound($response);
        }

        $post->set('html', $post->renderHtml());

        return $response->view('site/post', [
            'post' => $post,
        ]);
    }

    /**
     * Shows a 404 page.
     *
     * @param Response $response
     * @return Response
     */
    public function notFound(Response $response): Response
    {
        return $response->view('site/404');
    }
}