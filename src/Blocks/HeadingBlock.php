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
class HeadingBlock implements Block
{
    // The name of the block.
    public string $name = 'Heading';

    /**
     * Returns the editable Heading block.
     *
     * @param Post $post
     * @param array $block
     * @return string
     */
    public static function editable(Post $post, array $block): string
    {
        return (new Response)->view('admin/editor/blocks/heading', [
            'post' => $post->toArray(),
            'block' => $block,
        ])->send();
    }

    /**
     * Returns the viewable Heading block.
     *
     * @param Post $post
     * @param array $block
     * @return string
     */
    public static function viewable(Post $post, array $block): string
    {
        return <<<HTML
            <h2 class="block heading-block">
                {$block['value']}
            </h2>
        HTML;
    }
}