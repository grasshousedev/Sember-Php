<?php

namespace Asko\Sember\Controllers;

use Asko\Sember\Database;
use Asko\Sember\Models\Meta;
use Asko\Sember\Models\Post;
use Asko\Sember\Response;
use Parsedown;

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
                query: 'where status = ? order by published_at desc limit 10',
                data: ['published']
            )->map(function (Post $post) {
                $post->set('html', $post->renderHtml());

                return $post;
            })->orderBy('published_at', 'desc')->toArray();

        $site_title = $this->db->findOne(Meta::class, 'where meta_name = ?', ['site_name']);
        $site_description = $this->db->findOne(Meta::class, 'where meta_name = ?', ['site_description']);

        return $response->view('site/home', [
            'page_title' => false,
            'posts' => $posts,
            'site_name' => $site_title->get('meta_value'),
            'site_description' => (new Parsedown)->text($site_description?->get('meta_value') ?? ''),
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

        // Increment the views.
        $viewed_cookie_k = 'has_read_' . $post->get('id');

        if (!isset($_COOKIE[$viewed_cookie_k])) {
            setcookie($viewed_cookie_k, 'yes', time() + 60 * 60 * 24 * 30);
            $post->set('views', ($post->get('views') ?? 0) + 1);
            $this->db->update($post);
        }

        $post->set('html', $post->renderHtml());
        $site_title = $this->db->findOne(Meta::class, 'where meta_name = ?', ['site_name']);
        $site_description = $this->db->findOne(Meta::class, 'where meta_name = ?', ['site_description']);

        return $response->view('site/post', [
            'page_title' => $post->get('title'),
            'post' => $post,
            'site_name' => $site_title->get('meta_value'),
            'site_description' => (new Parsedown)->text($site_description?->get('meta_value') ?? ''),
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