<?php

namespace Sember\System\Blocks;

use Sember\System\Models\Post;
use Sember\System\Response;

/**
 * The Markdown block.
 *
 * @since 0.1.0
 */
class MarkdownBlock implements Block
{
    // The name of the block.
    public string $name = 'Markdown';

    // The icon of the block.
    public string $icon = 'fa-brands fa-markdown';

    // Injected JS
    public array $js = [
        '/system/js/blocks/markdown.js'
    ];

    // Injected CSS
    public array $css = [
        '/system/css/blocks/markdown.css'
    ];

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
        return (new Response)->systemView('blocks/markdown_editable', [
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
        return (new \Parsedown())->text($block['value']);
    }
}