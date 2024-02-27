<?php

namespace Asko\Sember\Blocks;

use Asko\Sember\Models\Post;
use Asko\Sember\Response;
use Exception;

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
    public array $js = [
        '/assets/shared/codemirror/codemirror.js',
        '/assets/shared/codemirror/mode/javascript/javascript.js',
        '/assets/shared/codemirror/mode/css/css.js',
        '/assets/shared/codemirror/mode/htmlmixed/htmlmixed.js',
        '/assets/shared/codemirror/mode/xml/xml.js',
        '/assets/shared/codemirror/mode/php/php.js',
        '/assets/admin/js/blocks/code.js'
    ];

    // Injected CSS
    public array $css = [
        '/assets/shared/codemirror/codemirror.css',
        '/assets/shared/codemirror/theme/idea.css',
        '/assets/admin/css/blocks/code.css'
    ];

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
        return [
            'language' => [
            ]
        ];
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
     * @throws Exception
     */
    public static function viewable(Post $post, array $block): string
    {
        $hl = new \Highlight\Highlighter();
        $hl->setAutodetectLanguages(['php', 'javascript', 'css', 'html', 'rust', 'clojure']);
        $hled = $hl->highlightAuto($block['value']);

        return (new Response)->view('site/blocks/code', [
            'post' => $post->toArray(),
            'block' => $block,
            'language' => $hled->language,
            'code' => $hled->value,
        ])->send();
    }
}