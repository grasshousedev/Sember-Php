<?php

namespace Asko\Nth\Controllers;

use Asko\Nth\DB;
use Asko\Nth\Models\Post;
use Asko\Nth\Request;
use Asko\Nth\Response;
use Exception;
use Ramsey\Uuid\Uuid;

class AdminAPIController extends Controller
{
    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->setupGuard();
        $this->authenticatedGuard();
    }

    public function editor(Response $response, string $id): Response
    {
        $post = DB::find(Post::class, ['id' => $id]);

        return $response->view('admin/editor', [
            'post' => $post,
            'blocks' => json_decode($post->get('content'), true) ?? []
        ]);
    }

    public function updateTitle(Request $request, Response $response, string $id): Response
    {
        $post = DB::find(Post::class, ['id' => $id]);
        $post->set('title', $request->input('title'));
        DB::update($post);

        return $response->json(['status' => 'success']);
    }

    public function addBlock(Request $request, Response $response, string $id): Response
    {
        $post = DB::find(Post::class, ['id' => $id]);

        $blocks = json_decode($post->get('content'), true) ?? [];
        $blocks[] = [
            'id' => Uuid::uuid4()->toString(),
            'type' => $request->input('type') ?? 'text',
            'value' => '',
        ];

        $post->set('content', json_encode($blocks));
        DB::update($post);

        return $response->view('admin/editor/blocks', [
            'blocks' => $blocks,
        ]);
    }
}