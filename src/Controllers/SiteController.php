<?php

namespace Asko\Sember\Controllers;

use Asko\Sember\Database;
use Asko\Sember\Models\Meta;
use Asko\Sember\Models\Post;
use Asko\Sember\Response;

readonly class SiteController
{
    public function __construct(private Database $db)
    {
    }

    /**
     * Shows the home page.
     *
     * @param Response $response
     * @return Response
     */
    public function home(Response $response): Response
    {
        $posts = $this->db
            ->find(
                model: Post::class,
                query: 'where status = ? order by created_at desc limit 10',
                data: ['published']
            )->map(function (Post $post) {
                $post->set('html', $post->renderHtml());

                return $post;
            })->toArray();

        $site = $this->db->findOne(Meta::class, 'where meta_name = ?', ['site_name']);

        if (!$site) {
            return $this->notFound($response);
        }

        return $response->view('site/home', [
            'page_title' => false,
            'posts' => $posts,
            'site_name' => $site->get('meta_value'),
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
        $post = $this->db->findOne(Post::class, 'where slug = ?', [$slug]);

        if (!$post) {
            return $this->notFound($response);
        }

        $post->set('html', $post->renderHtml());
        $site = $this->db->findOne(Meta::class, 'where meta_name = ?', ['site_name']);

        if (!$site) {
            return $this->notFound($response);
        }

        return $response->view('site/post', [
            'page_title' => $post->get('title'),
            'post' => $post,
            'site_name' => $site->get('meta_value'),
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