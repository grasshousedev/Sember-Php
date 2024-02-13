<?php

namespace Asko\Sember\Controllers;

use Asko\Sember\Config;
use Asko\Sember\Database;
use Asko\Sember\Helpers\ArrayHelper;
use Asko\Sember\Helpers\BlockHelper;
use Asko\Sember\Models\Post;
use Asko\Sember\Request;
use Asko\Sember\Response;

readonly class AdminAPIController
{
    public function __construct(private Database $db)
    {
    }

    /**
     * Get the editor for a post.
     *
     * @param Request $request
     * @param Response $response
     * @param string $id
     * @return Response
     */
    public function editor(Request $request, Response $response, string $id): Response
    {
        $post = $this->db->findOne(Post::class, 'where id = ?', [$id]);

        if (!$post) {
            return $response->json([
                'status' => 'error',
                'message' => 'Post not found.'
            ]);
        }

        return $response->view('admin/editor/blocks', [
            'url' => $request->protocol() . '://' . $request->hostname(),
            'post' => $post,
            'blocks' => BlockHelper::editableBlocks($post),
            'block_list' => BlockHelper::list(),
        ]);
    }

    /**
     * Get the status of a post.
     *
     * @param Response $response
     * @param string $id
     * @return Response
     */
    public function status(Response $response, string $id): Response
    {
        $post = $this->db->findOne(Post::class, 'where id = ?', [$id]);

        if (!$post) {
            return $response->json([
                'status' => 'error',
                'message' => 'Post not found.'
            ]);
        }

        return $response->make($post->get('status'));
    }

    /**
     * Get the published at for a post.
     *
     * @param Response $response
     * @param string $id
     * @return Response
     */
    public function publishedAt(Response $response, string $id): Response
    {
        $post = $this->db->findOne(Post::class, 'where id = ?', [$id]);

        if (!$post) {
            return $response->json([
                'status' => 'error',
                'message' => 'Post not found.'
            ]);
        }

        return $response->view('admin/editor/post-published-at', [
            'post' => $post,
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
        $post = $this->db->findOne(Post::class, 'where id = ?', [$id]);
        $post->set('title', $request->input('title'));
        $post->set('updated_at', time());
        $this->db->update($post);

        return $response->json(['status' => 'success']);
    }

    /**
     * Update the slug of a post.
     *
     * @param Request $request
     * @param Response $response
     * @param string $id
     * @return Response
     */
    public function updateSlug(Request $request, Response $response, string $id): Response
    {
        $post = $this->db->findOne(Post::class, 'where id = ?', [$id]);
        $post->set('slug', $request->input('slug'));
        $post->set('updated_at', time());
        $this->db->update($post);

        return $response->json(['status' => 'success']);
    }

    /**
     * Update the status of a post.
     *
     * @param Request $request
     * @param Response $response
     * @param string $id
     * @return Response
     */
    public function updateStatus(Request $request, Response $response, string $id): Response
    {
        $post = $this->db->findOne(Post::class, 'where id = ?', [$id]);

        if (!$post) {
            return $response->json([
                'status' => 'error',
                'message' => 'Post not found.'
            ]);
        }

        $post->set('status', $request->input('status'));
        $post->set('updated_at', time());
        $this->db->update($post);

        if ($post->get('status') !== 'published') {
            return $response->make('');
        }

        return $response->view('admin/editor/post-published-at', [
            'post' => $post,
        ]);
    }

    /**
     * Update the published at of a post.
     *
     * @param Request $request
     * @param Response $response
     * @param string $id
     * @return Response
     */
    public function updatePublishedAt(Request $request, Response $response, string $id): Response
    {
        $post = $this->db->findOne(Post::class, 'where id = ?', [$id]);

        if (!$post) {
            return $response->json([
                'status' => 'error',
                'message' => 'Post not found.'
            ]);
        }

        $post->set('published_at', strtotime($request->input('published_at')));
        $post->set('updated_at', time());
        $this->db->update($post);

        return $response->json(['status' => 'success']);
    }

    /**
     * Add a block to a post.
     *
     * @param Request $request
     * @param Response $response
     * @param string $id
     * @param string $type
     * @param string $position
     * @return Response
     */
    public function addBlock(
        Request  $request,
        Response $response,
        string   $id,
        string   $type,
        string   $position
    ): Response
    {
        $post = $this->db->findOne(Post::class, 'where id = ?', [$id]);
        $block = BlockHelper::new($type);
        $content = json_decode($post->get('content'), true);
        $blocks = match ($position) {
            'beginning' => [$block, ...$content],
            'end' => [...$content, $block],
            default => ArrayHelper::insertAfter($content, function ($block) use ($position) {
                return $block['id'] === $position;
            }, $block),
        };

        $post->set('content', json_encode($blocks));
        $post->set('updated_at', time());
        $this->db->update($post);

        return $response->view('admin/editor/blocks', [
            'url' => $request->protocol() . '://' . $request->hostname(),
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
        Request  $request,
        Response $response,
        string   $id,
        string   $blockId
    ): Response
    {
        $post = $this->db->findOne(Post::class, 'where id = ?', [$id]);

        if (!$post) {
            return $response->json([
                'status' => 'error',
                'message' => 'Post not found.'
            ]);
        }

        $blocks = json_decode($post->get('content'), true);
        $block_index = ArrayHelper::findIndex($blocks, fn($block) => $block['id'] === $blockId);
        $block = $blocks[$block_index];
        $blocks[$block_index] = [...$block, 'value' => $request->input('value')];
        $post->set('content', json_encode($blocks));
        $post->set('updated_at', time());
        $this->db->update($post);

        return $response->json(['status' => 'success']);
    }

    /**
     * Delete a block from a post.
     *
     * @param Request $request
     * @param Response $response
     * @param string $id
     * @param string $blockId
     * @return Response
     */
    public function deleteBlock(
        Request  $request,
        Response $response,
        string   $id,
        string   $blockId
    ): Response
    {
        $post = $this->db->findOne(Post::class, 'where id = ?', [$id]);

        if (!$post) {
            return $response->json([
                'status' => 'error',
                'message' => 'Post not found.'
            ]);
        }

        $blocks = json_decode($post->get('content'), true);
        $blocks = array_values(array_filter($blocks, fn($block) => $block['id'] !== $blockId));
        $post->set('content', json_encode($blocks));
        $post->set('updated_at', time());
        $this->db->update($post);

        return $response->view('admin/editor/blocks', [
            'url' => $request->protocol() . '://' . $request->hostname(),
            'post' => $post,
            'blocks' => BlockHelper::editableBlocks($post),
            'block_list' => BlockHelper::list(),
        ]);
    }

    /**
     * Move a block in a post.
     *
     * @param Request $request
     * @param Response $response
     * @param string $id
     * @param string $blockId
     * @param string $direction
     * @return Response
     */
    public function moveBlock(
        Request  $request,
        Response $response,
        string   $id,
        string   $blockId,
        string   $direction
    ): Response
    {
        $post = $this->db->findOne(Post::class, 'where id = ?', [$id]);

        if (!$post) {
            return $response->json([
                'status' => 'error',
                'message' => 'Post not found.'
            ]);
        }

        $blocks = json_decode($post->get('content'), true);
        $post->set('content', match ($direction) {
            'up' => ArrayHelper::moveUp($blocks, fn($block) => $block['id'] === $blockId),
            'down' => ArrayHelper::moveDown($blocks, fn($block) => $block['id'] === $blockId),
            default => $post->get('content'),
        });

        $post->set('updated_at', time());
        $this->db->update($post);

        return $response->view('admin/editor/blocks', [
            'url' => $request->protocol() . '://' . $request->hostname(),
            'post' => $post,
            'blocks' => BlockHelper::editableBlocks($post),
            'block_list' => BlockHelper::list(),
        ]);
    }

    /**
     * Block meta fn calling.
     *
     * @param Response $response
     * @param string $id
     * @param string $blockId
     * @param string $fn
     * @param string $arg
     * @return Response
     */
    public function blockOption(
        Response $response,
        string   $id,
        string   $blockId,
        string   $fn,
        string   $arg
    ): Response
    {
        $post = $this->db->findOne(Post::class, 'where id = ?', [$id]);

        if (!$post) {
            return $response->json([
                'status' => 'error',
                'message' => 'Post not found.'
            ]);
        }

        $blocks = json_decode($post->get('content'), true);
        $block_index = ArrayHelper::findIndex($blocks, fn($block) => $block['id'] === $blockId);
        $block = $blocks[$block_index];
        $class = Config::getBlock($block['key']);

        return call_user_func([$class, $fn], $post, $block, $arg);
    }
}
