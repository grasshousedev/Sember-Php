<?php

namespace Asko\Sember\Blocks;

use Asko\Sember\Models\Post;
use Asko\Sember\Response;

class ParagraphBlock implements Block
{
    // The name of the block.
    public string $name = 'Paragraph';

    // The icon of the block.
    public string $icon = 'fa-solid fa-paragraph';

    // Injected JS
    public array $js = [
        '/assets/admin/js/blocks/paragraph.js'
    ];

    // Injected CSS
    public array $css = [
        '/assets/admin/css/blocks/paragraph.css'
    ];

    public static function editable(Post $post, array $block): string
    {
        return (new Response)->view('admin/editor/blocks/paragraph', [
            'post' => $post->toArray(),
            'block' => $block,
        ])->send();
    }

    public static function viewable(Post $post, array $block): string
    {
        // TODO: Implement viewable() method.
    }

    public static function model(): array
    {
        return [];
    }

    public static function options(Post $post, array $block): array
    {
        return [];
    }
}