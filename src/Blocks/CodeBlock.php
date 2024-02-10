<?php

namespace Asko\Sember\Blocks;

use Asko\Sember\Models\Post;
use Asko\Sember\Response;

/**
 * The code block.
 *
 * @package Asko\Nth\Blocks
 * @since 0.1.0
 */
class CodeBlock implements Block
{
    // The name of the block.
    public string $name = 'Code';

    // The icon of the block.
    public string $icon = 'fa-solid fa-code';

    // Injected JS
    public array $js = ['/assets/admin/js/blocks/code.js'];

    /**
     * Returns the data model for the heading block.
     *
     * @return array
     */
    public static function model(): array
    {
        return [];
    }

    /**
     * Returns the options for the heading block.
     *
     * @param Post $post
     * @param array $block
     * @return array
     */
    public static function options(Post $post, array $block): array
    {
        return [];
    }

    /**
     * Returns the editable heading block.
     *
     * @param Post $post
     * @param array $block
     * @return string
     */
    public static function editable(Post $post, array $block): string
    {
        return (new Response)->view('admin/editor/blocks/code', [
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
            <pre class="block code-block">
                <code>{$block['value']}</code>
            </pre>
        HTML;
    }
}