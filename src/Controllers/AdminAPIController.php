<?php

namespace Asko\Nth\Controllers;

use Asko\Nth\DB;
use Asko\Nth\Helpers\ArrayHelper;
use Asko\Nth\Helpers\BlockHelper;
use Asko\Nth\Models\Post;
use Asko\Nth\Request;
use Asko\Nth\Response;
use Exception;

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

    /**
     * Get the editor for a post.
     *
     * @param Response $response
     * @param string $id
     * @return Response
     */
    public function editor(Response $response, string $id): Response
    {
        $post = DB::find(Post::class, ['id' => $id]);

        return $response->view('admin/editor', [
            'post' => $post,
            'blocks' => json_decode($post->get('content'), true) ?? []
        ]);
    }

    /**
     * Update the title of a post.
     *
     * @param Request $request
     * @param Response $response
     * @param string $id
     * @return Response
     */
    public function updateTitle(Request $request, Response $response, string $id): Response
    {
        $post = DB::find(Post::class, ['id' => $id]);
        $post->set('title', $request->input('title'));

        DB::update($post);

        return $response->json(['status' => 'success']);
    }

    /**
     * Add a block to a post.
     *
     * @param Response $response
     * @param string $id
     * @param string $type
     * @param string $position
     * @return Response
     */
    public function addBlock(
        Response $response,
        string $id,
        string $type,
        string $position
    ): Response {
        $post = DB::find(Post::class, ['id' => $id]);
        $block = BlockHelper::new($type);
        $blocks = json_decode($post->get('content'), true) ?? [];
        $blocks = match ($position) {
            'beginning' => [$block, ...$blocks],
            'end' => [...$blocks, $block],
            default => ArrayHelper::insertAfter($blocks, fn ($block) => $block['id'] === $position, $block),
        };

        $post->set('content', json_encode($blocks));

        DB::update($post);

        return $response->view('admin/editor/blocks', [
            'post' => $post,
            'blocks' => $blocks,
        ]);
    }

    /**
     * Update a block in a post.
     *
     * @param Request $request
     * @param Response $response
     * @param string $id
     * @param string $blockId
     * @return Response
     */
    public function updateBlock(
        Request $request,
        Response $response,
        string $id,
        string $blockId
    ): Response
    {
        $post = DB::find(Post::class, ['id' => $id]);
        $blocks = json_decode($post->get('content'), true) ?? [];
        $block_index = ArrayHelper::findIndex($blocks, fn ($block) => $block['id'] === $blockId);
        $block = $blocks[$block_index];
        $blocks[$block_index] = [...$block, 'value' => $request->input('value')];
        $post->set('content', json_encode($blocks));

        DB::update($post);

        return $response->json(['status' => 'success']);
    }

    /**
     * Delete a block from a post.
     *
     * @param Response $response
     * @param string $id
     * @param string $blockId
     * @return Response
     */
    public function deleteBlock(Response $response, string $id, string $blockId): Response
    {
        $post = DB::find(Post::class, ['id' => $id]);
        $blocks = json_decode($post->get('content'), true) ?? [];
        $blocks = array_filter($blocks, fn ($block) => $block['id'] !== $blockId);
        $post->set('content', json_encode($blocks));

        DB::update($post);

        return $response->view('admin/editor/blocks', [
            'post' => DB::find(Post::class, ['id' => $id]),
            'blocks' => $blocks,
        ]);
    }
}
