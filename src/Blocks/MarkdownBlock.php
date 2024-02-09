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

    // The icon of the block.
    public string $icon = 'fa-brands fa-markdown';

    /**
     * Returns the data model for the heading block.
     *
     * @return string[]
     */
    public static function model(): array
    {
        return [];
    }

    /**
     * Returns the options for the Markdown block.
     *
     * @param Post $post
     * @param array $block
     * @return array[]
     */
    public static function options(Post $post, array $block): array
    {
        return [];
    }

    /**
     * Returns the editable Markdown block.
     *
     * @param Post $post
     * @param array $block
     * @return string
     */
    public static function editable(Post $post, array $block): string
    {
        return (new Response)->view('admin/editor/blocks/markdown', [
            'post' => $post->toArray(),
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