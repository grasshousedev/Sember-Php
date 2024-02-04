<?php

namespace Asko\Nth\Controllers;

use Asko\Nth\DB;
use Asko\Nth\Models\Post;
use Asko\Nth\Request;
use Asko\Nth\Response;
use Exception;

class AdminController extends Controller
{
    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->setupGuard();
        $this->authenticatedGuard();
    }

    public function index(Request $request, Response $response): Response
    {
        if (!$request->session()->has('auth_token')) {
            return $response->redirect('/admin/signin');
        }

        return $response->redirect('/admin/posts');
    }

    public function posts(Response $response): Response
    {
        $posts = DB::findAll(Post::class)
            ->orderBy('created_at', 'desc')
            ->toArray();

        return $response->view('admin/posts', [
            'posts' => $posts,
        ]);
    }

    public function createPost(Response $response): Response
    {
        if ($id = DB::create(new Post([
            'title' => 'Untitled ...',
            'slug' => '',
            'content' => 'New post content',
            'status' => 'draft',
        ]))) {
            return $response->redirect("/admin/posts/edit/{$id}");
        };

        return $response->redirect("/admin/posts");
    }

    public function editPost(Response $response, string $id): Response
    {
        return $response->view('admin/edit-post', [
            'id' => $id,
        ]);
    }

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