<?php

namespace Asko\Sember\Controllers;

use Asko\Sember\DB;
use Asko\Sember\Helpers\BlockHelper;
use Asko\Sember\Models\Post;
use Asko\Sember\Request;
use Asko\Sember\Response;
use Exception;

class AdminController
{
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
        if (!$request->session()->has('auth_token')) {
            return $response->redirect('/admin/signin');
        }

        return $response->redirect('/admin/posts');
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
        $request->session()->remove('auth_token');

        return $response->redirect('/admin/signin');
    }

    /**
     * List all posts.
     *
     * @param Response $response
     * @return Response
     */
    public function posts(Response $response): Response
    {
        $posts = DB::findAll(Post::class)
            ->orderBy('created_at', 'desc')
            ->toArray();

        return $response->view('admin/posts', [
            'posts' => $posts,
        ]);
    }

    /**
     * Create a new post.
     *
     * @param Response $response
     * @return Response
     */
    public function createPost(Response $response): Response
    {
        if ($id = DB::create(new Post([
            'title' => 'Untitled ...',
            'slug' => 'untitled',
            'content' => [BlockHelper::new('markdown')],
            'status' => 'draft',
            'created_at' => time(),
            'updated_at' => time(),
            'published_at' => null,
        ]))) {
            return $response->redirect("/admin/posts/edit/{$id}");
        };

        return $response->redirect("/admin/posts");
    }

    /**
     * Edit a post.
     *
     * @param Response $response
     * @param string $id
     * @return Response
     */
    public function editPost(Response $response, string $id): Response
    {
        $post = DB::find(Post::class, ['id' => $id]);

        if (!$post) {
            return $response->redirect('/admin/posts');
        }

        return $response->view('admin/edit-post', [
            'id' => $id,
            'post' => $post,
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
        $post = DB::find(Post::class, ['id' => $id]);

        if (!$post) {
            return $response->redirect('/admin/posts');
        }

        DB::delete($post);

        return $response->redirect('/admin/posts');
    }
}