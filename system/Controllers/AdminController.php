<?php

namespace Sember\System\Controllers;

use Sember\System\Config;
use Sember\System\Database;
use Sember\System\Helpers\BlockHelper;
use Sember\System\Models\Meta;
use Sember\System\Models\Post;
use Sember\System\Models\User;
use Sember\System\Request;
use Sember\System\Response;

readonly class AdminController
{
    public function __construct(private Database $db)
    {
    }

    /**
     * Redirect to the posts page if authenticated. Otherwise, redirect
     * to the sign-in page.
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function index(Request $request, Response $response): Response
    {
        if (!$request->session()->has("auth_token")) {
            return $response->redirect("/admin/signin");
        }

        $default_post_type = array_key_first(Config::get("post_types"));

        return $response->redirect("/admin/posts?type={$default_post_type}");
    }

    /**
     * Signs the user out.
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function signOut(Request $request, Response $response): Response
    {
        $request->cookie()->remove("auth_token");

        return $response->redirect("/admin/signin");
    }

    /**
     * List all posts.
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function posts(Request $request, Response $response): Response
    {
        if (!$request->input('type')) {
            $default_post_type = array_key_first(Config::get("post_types"));
            return $response->redirect("/admin/posts?type={$default_post_type}");
        }

        $status = match($request->input('status')) {
            'draft' => "'draft'",
            'published' => "'published'",
            default => 'status',
        };

        $sort_by = match($request->input('sort_by')) {
            'title', 'status', 'published_at', 'created_at', 'updated_at', 'views' => $request->input('sort_by'),
            default => 'created_at',
        };

        $sort_order = match($request->input('sort_order')) {
            'asc', 'desc' => $request->input('sort_order'),
            default => 'desc',
        };

        $query = "where type = ? and status = {$status} order by {$sort_by} {$sort_order}";
        $posts = Post::find($query, [$request->input('type')])
            ->map(function (Post $post) {
                if ($post->get("status") === "published" && $post->get("published_at") <= time()) {
                    $post->set("status", "published");
                } elseif ($post->get("status") === "published" && $post->get("published_at") > time()) {
                    $post->set("status", "scheduled");
                } else {
                    $post->set("status", "draft");
                }

                return $post;
            })
            ->toArray();

        return $response->systemView("admin/posts", [
            "post_type" => Config::get("post_types")[$request->input('type') ?? "post"],
            "post_type_key" => $request->input('type'),
            "post_types" => Config::get("post_types"),
            "filter_by_status" => $request->input("status", 'all'),
            "sort_by" => $request->input("sort_by", 'created_at'),
            "sort_order" => $request->input("sort_order", 'desc'),
            "posts" => $posts,
            "site_name" => Meta::getValue('site_name'),
            "url" => $request->protocol() . "://" . $request->hostname(),
            "url_without_protocol" => $request->hostname(),
        ]);
    }

    /**
     * Create a new post.
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function createPost(Request $request, Response $response): Response
    {
        $user = User::current();

        if (!$user) {
            return $response->redirect("/admin/signin");
        }

        if (
            $id = $this->db->create(
                new Post([
                    "title" => "",
                    "slug" => "",
                    "content" => json_encode([BlockHelper::new("paragraph")]),
                    "status" => "draft",
                    "type" => $request->input('type', 'post'),
                    "user_id" => $user->get("id"),
                    "created_at" => time(),
                    "updated_at" => time(),
                ])
            )
        ) {
            return $response->redirect("/admin/posts/edit/{$id}");
        }

        return $response->redirect("/admin/posts?type=" . $request->input('type'));
    }

    /**
     * Edit a post.
     *
     * @param Request $request
     * @param Response $response
     * @param string $id
     * @return Response
     */
    public function editPost(
        Request $request,
        Response $response,
        string $id
    ): Response {
        $post = Post::findOne("where id = ?", [$id]);
        $default_post_type = array_key_first(Config::get("post_types"));

        if (!$post) {
            return $response->redirect("/admin/posts?type={$default_post_type}");
        }

        return $response->systemView("admin/edit-post", [
            "id" => $id,
            "post" => $post,
            "injected_js" => BlockHelper::injectedJs(),
            "injected_css" => BlockHelper::injectedCss(),
            "url" => $request->protocol() . "://" . $request->hostname(),
            "url_without_protocol" => $request->hostname(),
            "blocks" => BlockHelper::editableBlocks($post),
            "block_list" => BlockHelper::list(),
            "site_name" => Meta::getValue('site_name'),
            "post_type" => Config::get("post_types")[$post->get('type') ?? $default_post_type],
            "post_type_key" => $request->input('type') ?? $default_post_type,
            "post_types" => Config::get("post_types"),
        ]);
    }

    /**
     * Delete a post.
     *
     * @param Response $response
     * @param string $id
     * @return Response
     */
    public function deletePost(Response $response, string $id): Response
    {
        $default_post_type = array_key_first(Config::get("post_types"));
        $post = Post::findOne("where id = ?", [$id]);

        if (!$post) {
            return $response->redirect("/admin/posts?type={$default_post_type}");
        }

        $post->delete();

        return $response->redirect("/admin/posts?type=" . $post->get("type"));
    }

    /**
     * Settings page.
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function settings(Request $request, Response $response): Response
    {
        return $response->systemView("admin/settings", [
            "site_name" => Meta::getValue('site_name'),
            "site_description" => Meta::getValue('site_description'),
            "url" => $request->protocol() . "://" . $request->hostname(),
            "url_without_protocol" => $request->hostname(),
            "post_types" => Config::get("post_types"),
        ]);
    }
}
