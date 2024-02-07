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

        if (!$post) {
            return $response->json([
                'status' => 'error',
                'message' => 'Post not found.'
            ]);
        }

        return $response->view('admin/editor', [
            'post' => $post,
            'blocks' => BlockHelper::editableBlocks($post),
            'block_list' => BlockHelper::list(),
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
        $blocks = match ($position) {
            'beginning' => [$block, ...$post->get('content')],
            'end' => [...$post->get('content'), $block],
            default => ArrayHelper::insertAfter($post->get('content'), function ($block) use ($position) {
                return $block['id'] === $position;
            }, $block),
        };

        $post->set('content', $blocks);

        DB::update($post);

        return $response->view('admin/editor/blocks', [
            'post' => $post,
            'blocks' => BlockHelper::editableBlocks($post),
            'block_list' => BlockHelper::list(),
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

        if (!$post) {
            return $response->json([
                'status' => 'error',
                'message' => 'Post not found.'
            ]);
        }

        $blocks = $post->get('content');
        $block_index = ArrayHelper::findIndex($blocks, fn ($block) => $block['id'] === $blockId);
        $block = $blocks[$block_index];
        $blocks[$block_index] = [...$block, 'value' => $request->input('value')];
        $post->set('content', $blocks);

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

        if (!$post) {
            return $response->json([
                'status' => 'error',
                'message' => 'Post not found.'
            ]);
        }

        $blocks = array_filter($post->get('content'), fn ($block) => $block['id'] !== $blockId);
        $post->set('content', $blocks);

        DB::update($post);

        return $response->view('admin/editor/blocks', [
            'post' => $post,
            'blocks' => BlockHelper::editableBlocks($post),
            'block_list' => BlockHelper::list(),
        ]);
    }

    /**
     * Move a block in a post.
     *
     * @param Response $response
     * @param string $id
     * @param string $blockId
     * @param string $direction
     * @return Response
     */
    public function moveBlock(
        Response $response,
        string $id,
        string $blockId,
        string $direction
    ): Response
    {
        $post = DB::find(Post::class, ['id' => $id]);

        if (!$post) {
            return $response->json([
                'status' => 'error',
                'message' => 'Post not found.'
            ]);
        }

        $post->set('content', match($direction) {
            'up' => ArrayHelper::moveUp($post->get('content'), fn ($block) => $block['id'] === $blockId),
            'down' => ArrayHelper::moveDown($post->get('content'), fn ($block) => $block['id'] === $blockId),
            default => $post->get('content'),
        });

        DB::update($post);

        return $response->view('admin/editor/blocks', [
            'post' => $post,
            'blocks' => BlockHelper::editableBlocks($post),
            'block_list' => BlockHelper::list(),
        ]);
    }
}
