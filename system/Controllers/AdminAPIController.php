<?php

namespace Sember\System\Controllers;

use Sember\System\Config;
use Sember\System\Database;
use Sember\System\Helpers\ArrayHelper;
use Sember\System\Helpers\BlockHelper;
use Sember\System\Models\Meta;
use Sember\System\Models\Post;
use Sember\System\Request;
use Sember\System\Response;

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
        $post = Post::findOne('where id = ?', [$id]);

        if (!$post) {
            return $response->json([
                'status' => 'error',
                'message' => 'Post not found.'
            ]);
        }

        return $response->systemView('admin/editor/blocks', [
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
        $post = Post::findOne('where id = ?', [$id]);

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
        $post = Post::findOne('where id = ?', [$id]);

        if (!$post) {
            return $response->json([
                'status' => 'error',
                'message' => 'Post not found.'
            ]);
        }

        return $response->systemView('admin/editor/post-published-at', [
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
        $post = Post::findOne('where id = ?', [$id]);
        $post->set('title', $request->input('title'));
        $post->set('updated_at', time());
        $post->update();

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
        $post = Post::findOne('where id = ?', [$id]);
        $post->set('slug', $request->input('slug'));
        $post->set('updated_at', time());
        $post->update();

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
        $post = Post::findOne('where id = ?', [$id]);

        if (!$post) {
            return $response->json([
                'status' => 'error',
                'message' => 'Post not found.'
            ]);
        }

        // If we're setting the post as published, but there's no published at
        // date set, set one.
        if (
            $post->get('status') === 'draft' &&
            !$post->get('published_at') &&
            $request->input('status') === 'published'
        ) {
            $post->set('published_at', time());
        }

        $post->set('status', $request->input('status'));
        $post->set('updated_at', time());
        $post->update();

        return $response->json(['status' => 'success']);
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
        $post = Post::findOne('where id = ?', [$id]);

        if (!$post) {
            return $response->json([
                'status' => 'error',
                'message' => 'Post not found.'
            ]);
        }

        $post->set('published_at', strtotime($request->input('published_at')));
        $post->set('updated_at', time());
        $post->update();

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
        $post = Post::findOne('where id = ?', [$id]);
        $opts = $request->input('content') ? ['value' => $request->input('content')] : [];
        $block = BlockHelper::new($type, $opts);
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
        $post->update();

        header('HX-Trigger-After-Settle: {"focusBlockBeginning": "' . $block['id'] . '"}');

        return $response->systemView('admin/editor/blocks', [
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
        $post = Post::findOne('where id = ?', [$id]);

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
        $post->update();

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
        $post = Post::findOne('where id = ?', [$id]);

        if (!$post) {
            return $response->json([
                'status' => 'error',
                'message' => 'Post not found.'
            ]);
        }

        $blocks = json_decode($post->get('content'), true);
        $prev_block = ArrayHelper::findPrev($blocks, fn($block) => $block['id'] === $blockId);
        $blocks = array_values(array_filter($blocks, fn($block) => $block['id'] !== $blockId));
        $post->set('content', json_encode($blocks));
        $post->set('updated_at', time());
        $post->update();

        if ($prev_block) {
            header('HX-Trigger-After-Settle: {"focusBlockEnd": "' . $prev_block['id'] . '"}');
        }

        return $response->systemView('admin/editor/blocks', [
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
        $post = Post::findOne('where id = ?', [$id]);

        if (!$post) {
            return $response->json([
                'status' => 'error',
                'message' => 'Post not found.'
            ]);
        }

        $blocks = json_decode($post->get('content'), true);
        $post->set('content', match ($direction) {
            'up' => json_encode(ArrayHelper::moveUp($blocks, fn($block) => $block['id'] === $blockId)),
            'down' => json_encode(ArrayHelper::moveDown($blocks, fn($block) => $block['id'] === $blockId)),
            default => $post->get('content'),
        });

        $post->set('updated_at', time());
        $post->update();

        return $response->systemView('admin/editor/blocks', [
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
     * @param string|null $arg
     * @return Response
     */
    public function blockOption(
        Response $response,
        string   $id,
        string   $blockId,
        string   $fn,
        ?string  $arg,
    ): Response
    {
        $post = Post::findOne('where id = ?', [$id]);

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

    /**
     * Update the site name.
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function updateSiteName(Request $request, Response $response): Response
    {
        $site_name = Meta::findOne('where meta_name = ?', ['site_name']);
        $site_name->set('meta_value', $request->input('name', ''));
        $site_name->update();

        return $response->json(['status' => 'success']);
    }

    /**
     * Update the site description.
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function updateSiteDescription(Request $request, Response $response): Response
    {
        $site_description = Meta::findOne('where meta_name = ?', ['site_description']);

        // Description doesn't exist yet, create it.
        if (!$site_description) {
            $site_description = new Meta([
                'meta_name' => 'site_description',
                'meta_value' => $request->input('description', ''),
                'created_at' => time(),
                'updated_at' => time(),
            ]);

            $this->db->create($site_description);

            return $response->json(['status' => 'success']);
        }

        // Description exists, update it.
        $site_description->set('meta_value', $request->input('description', ''));
        $site_description->update();

        return $response->json(['status' => 'success']);
    }
}
