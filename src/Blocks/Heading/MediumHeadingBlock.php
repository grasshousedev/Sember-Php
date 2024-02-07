<?php

namespace Asko\Nth\Blocks\Heading;

use Asko\Nth\Blocks\Block;
use Asko\Nth\Models\Post;
use Asko\Nth\Response;

/**
 * The medium heading block.
 *
 * @package Asko\Nth\Blocks
 * @since 0.1.0
 */
class MediumHeadingBlock implements Block
{
    // The name of the block.
    public string $name = 'Medium Heading';

    /**
     * Returns the editable heading block.
     *
     * @param Post $post
     * @param array $block
     * @return string
     */
    public static function editable(Post $post, array $block): string
    {
        return (new Response)->view('admin/editor/blocks/medium-heading', [
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
            <h3 class="block heading-block">
                {$block['value']}
            </h3>
        HTML;
    }
}