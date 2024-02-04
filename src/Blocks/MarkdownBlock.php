<?php

namespace Asko\Nth\Blocks;

use Asko\Nth\Models\Post;

/**
 * The Markdown block.
 *
 * @package Asko\Nth\Blocks
 * @since 0.1.0
 */
class MarkdownBlock implements Block
{
    // The name of the block.
    protected string $name = 'Markdown';

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

        return <<<HTML
            <div class="block markdown-block" data-id="{$block['id']}">
                <textarea 
                    name="value"
                    hx-post="/admin/api/post/{$post['id']}/blocks/{$block['id']}"
                    hx-trigger="input changed delay:250ms">{$block['value']}</textarea>
            </div>
        HTML;
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