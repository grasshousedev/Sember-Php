<?php

namespace Asko\Nth\Blocks;

use Asko\Nth\Models\Post;
use Asko\Nth\Response;

/**
 * The Markdown block.
 *
 * @package Asko\Nth\Blocks
 * @since 0.1.0
 */
class MarkdownBlock implements Block
{
    // The name of the block.
    public string $name = 'Markdown';

    /**
     * Returns the editable Markdown block.
     *
     * @param Post $post
     * @param array $block
     * @return string
     */
    public static function editable(Post $post, array $block): string
    {
        $post = $post->toArray();

        return (new Response)->view('admin/editor/blocks/markdown', [
            'post' => $post,
            'block' => $block,
        ])->send();
    }

    /**
     * Returns the viewable Markdown block.
     *
     * @param Post $post
     * @param array $block
     * @return string
     */
    public static function viewable(Post $post, array $block): string
    {
        return <<<HTML
            <div class="block markdown-block" data-id="{$block['id']}">
                {$block['value']}
            </div>
        HTML;
    }
}