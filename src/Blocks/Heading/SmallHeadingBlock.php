<?php

namespace Asko\Nth\Blocks\Heading;

use Asko\Nth\Blocks\Block;
use Asko\Nth\Models\Post;
use Asko\Nth\Response;

/**
 * The small heading block.
 *
 * @package Asko\Nth\Blocks
 * @since 0.1.0
 */
class SmallHeadingBlock implements Block
{
    // The name of the block.
    public string $name = 'Small Heading';

    /**
     * Returns the editable heading block.
     *
     * @param Post $post
     * @param array $block
     * @return string
     */
    public static function editable(Post $post, array $block): string
    {
        return (new Response)->view('admin/editor/blocks/small-heading', [
            'post' => $post->toArray(),
            'block' => $block,
        ])->send();
    }

    /**
     * Returns the viewable heading block.
     *
     * @param Post $post
     * @param array $block
     * @return string
     */
    public static function viewable(Post $post, array $block): string
    {
        return <<<HTML
            <h4 class="block heading-block">
                {$block['value']}
            </h4>
        HTML;
    }
}