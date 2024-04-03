<?php

namespace Sember\App\Controllers;

use Sember\System\Database;
use Sember\System\Models\Meta;
use Sember\System\Models\Post;
use Sember\System\Models\User;
use Sember\System\Request;
use Sember\System\Response;
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
    public function home(Request $request, Response $response, ?int $page = 1): Response
    {
        $offset = ($page - 1) * 10;
        $limit = 10;

        // Total posts
        $total_posts = $this->db->count(
            model: Post::class,
            query: "where type = ? and status = ? and published_at <= ?",
            data: ["post", "published", time()]
        );

        // Posts
        $posts = Post::findAll(
                query: "where type = ? and status = ? and published_at <= ? order by published_at desc limit ? offset ?",
                params: ["post", "published", time(), $limit, $offset]
            )
            ->map(function (Post $post) {
                $post->set("html", $post->renderHtml());

                return $post;
            })
            ->toArray();

        $site_title = Meta::find("where meta_name = ?", ["site_name"]);

        return $response->view("home", [
            "page_title" => false,
            "posts" => $posts,
            "site_name" => $site_title->get("meta_value"),
            "pagination" => [
                "total_pages" => ceil($total_posts / $limit),
                "current_page" => $page,
                "prev_page" => $page > 1 ? $page - 1 : null,
                "next_page" => $page < ceil($total_posts / $limit) ? ($page ?? 1) + 1 : null,
            ]
        ]);
    }

    /**
     * Shows a post.
     *
     * @param Request $request
     * @param Response $response
     * @param string $slug
     * @return Response
     */
    public function post(Request $request, Response $response, string $slug): Response
    {
        if (User::current()) {
            $post = Post::find(
                query: "where slug = ?",
                params: [$slug]
            );
        } else {
            $post = Post::find(
                query: "where slug = ? and status = ? and published_at <= ?",
                params: [$slug, "published", time()]
            );
        }

        if (!$post) {
            return $this->notFound($response);
        }

        // Increment the views.
        $viewed_cookie_k = "has_read_" . $post->get("id");

        if (!$request->cookie()->has($viewed_cookie_k)) {
            $request->cookie()->set($viewed_cookie_k, "yes", time() + 60 * 60 * 24 * 30);
            $post->set("views", ($post->get("views") ?? 0) + 1);
            $this->db->update($post);
        }

        $post->set("html", $post->renderHtml());

        $site_title = Meta::find(
            query: "where meta_name = ?",
            params: ["site_name"]
        );

        $post_type = $post->get("type") ?? "post";

        return $response->view("{$post_type}", [
            "page_title" => $post->get("title"),
            "post" => $post,
            "site_name" => $site_title->get("meta_value"),
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
        return $response->view("404", [
            "page_title" => "Not Found",
            "site_name" => Meta::find("where meta_name = ?", ["site_name"])?->get("meta_value") ?? "",
        ]);
    }
}
