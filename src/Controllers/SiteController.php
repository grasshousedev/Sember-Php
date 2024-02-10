<?php

namespace Asko\Sember\Controllers;

use Asko\Sember\DB;
use Asko\Sember\Models\Meta;
use Asko\Sember\Models\Post;
use Asko\Sember\Response;

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
            })->toArray();

        $site = DB::find(Meta::class, ['meta_name' => 'site_config']);

        if (!$site) {
            return $this->notFound($response);
        }

        return $response->view('site/home', [
            'page_title' => false,
            'posts' => $posts,
            ...$site->toArray(),
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
        $site = DB::find(Meta::class, ['meta_name' => 'site_config']);

        if (!$site) {
            return $this->notFound($response);
        }

        return $response->view('site/post', [
            'page_title' => $post->get('title'),
            'post' => $post,
            ...$site->toArray(),
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