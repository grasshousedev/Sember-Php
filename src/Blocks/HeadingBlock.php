<?php

namespace Asko\Sember\Blocks;

use Asko\Sember\Blocks\Block;
use Asko\Sember\Database;
use Asko\Sember\DB;
use Asko\Sember\Helpers\ArrayHelper;
use Asko\Sember\Helpers\BlockHelper;
use Asko\Sember\Models\Post;
use Asko\Sember\Response;
use Parsedown;

/**
 * The heading block.
 *
 * @package Asko\Nth\Blocks
 * @since 0.1.0
 */
class HeadingBlock implements Block
{
    // The name of the block.
    public string $name = 'Heading';

    // The icon of the block.
    public string $icon = 'fa-solid fa-heading';

    // Injected JS
    public array $js = [
        '/assets/admin/js/blocks/heading.js'
    ];

    // Injected CSS
    public array $css = [
        '/assets/admin/css/blocks/heading.css'
    ];

    /**
     * Returns the data model for the heading block.
     *
     * @return array
     */
    public static function model(): array
    {
        return [
            'size' => 'big',
        ];
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
        $post = $post->toArray();

        return [
            'big' => [
                'name' => 'Big',
                'icon' => 'fa-solid fa-heading',
                'url' => "/admin/api/post/{$post['id']}/blocks/{$block['id']}/opt/size/big",
                'swap' => 'outerHTML',
                'target' => '.blocks',
                'active' => $block['size'] === 'big'
            ],
            'medium' => [
                'name' => 'Medium',
                'icon' => 'fa-solid fa-heading',
                'url' => "/admin/api/post/{$post['id']}/blocks/{$block['id']}/opt/size/medium",
                'swap' => 'outerHTML',
                'target' => '.blocks',
                'active' => $block['size'] === 'medium'
            ],
            'small' => [
                'name' => 'Small',
                'icon' => 'fa-solid fa-heading',
                'url' => "/admin/api/post/{$post['id']}/blocks/{$block['id']}/opt/size/small",
                'swap' => 'outerHTML',
                'target' => '.blocks',
                'active' => $block['size'] === 'small'
            ],
        ];
    }

    /**
     * Changes the size of the heading block.
     *
     * @param Post $post
     * @param array $block
     * @param string $size
     * @return Response
     */
    public static function size(Post $post, array $block, string $size): Response
    {
        $blocks = json_decode($post->get('content'), true);
        $block_index = ArrayHelper::findIndex($blocks, fn($i) => $i['id'] === $block['id']);
        $block = $blocks[$block_index];
        $blocks[$block_index] = [...$block, 'size' => $size];
        $post->set('content', json_encode($blocks));

        (new Database)->update($post);

        return (new Response)->view('admin/editor/blocks', [
            'post' => $post,
            'blocks' => BlockHelper::editableBlocks($post),
            'block_list' => BlockHelper::list(),
        ]);
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
        return (new Response)->view('admin/editor/blocks/heading', [
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
        $size = match ($block['size']) {
            'big' => 'h2',
            'medium' => 'h3',
            'small' => 'h4',
        };

        $value = (new Parsedown)->line($block['value']);

        return <<<HTML
            <{$size} class="block heading-block">
                {$value}
            </{$size}>
        HTML;
    }
}